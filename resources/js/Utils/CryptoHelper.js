async function deriveMasterKey(password, salt) {
    const encoder = new TextEncoder();
    const passwordKey = await window.crypto.subtle.importKey(
        "raw",
        encoder.encode(password),
        { name: "PBKDF2" },
        false,
        ["deriveKey"],
    );

    return await window.crypto.subtle.deriveKey(
        {
            name: "PBKDF2",
            salt: salt,
            iterations: 100000,
            hash: "SHA-256",
        },
        passwordKey,
        { name: "AES-GCM", length: 256 },
        false,
        ["encrypt", "decrypt"],
    );
}

export async function generateKeyPair() {
    const keyPair = await window.crypto.subtle.generateKey(
        { name: "ECDH", namedCurve: "P-256" },
        true,
        ["deriveKey", "deriveBits"],
    );

    const publicKeyJwk = await window.crypto.subtle.exportKey(
        "jwk",
        keyPair.publicKey,
    );
    const privateKeyJwk = await window.crypto.subtle.exportKey(
        "jwk",
        keyPair.privateKey,
    );

    return { publicKeyJwk, privateKeyJwk };
}

export async function encryptPrivateKeyWithPassword(privateKeyJwk, password) {
    const encoder = new TextEncoder();
    const privateKeyString = JSON.stringify(privateKeyJwk);

    const salt = window.crypto.getRandomValues(new Uint8Array(16));
    const iv = window.crypto.getRandomValues(new Uint8Array(12));

    const masterKey = await deriveMasterKey(password, salt);

    const encryptedBuffer = await window.crypto.subtle.encrypt(
        { name: "AES-GCM", iv: iv },
        masterKey,
        encoder.encode(privateKeyString),
    );

    return {
        encrypted_private_key: btoa(
            String.fromCharCode(...new Uint8Array(encryptedBuffer)),
        ),
        salt: btoa(String.fromCharCode(...salt)),
        iv: btoa(String.fromCharCode(...iv)),
    };
}

export async function decryptPrivateKeyWithPassword(payload, password) {
    const decoder = new TextDecoder();

    const encryptedBuffer = Uint8Array.from(
        atob(payload.encrypted_private_key),
        (c) => c.charCodeAt(0),
    );
    const salt = Uint8Array.from(atob(payload.salt), (c) => c.charCodeAt(0));
    const iv = Uint8Array.from(atob(payload.iv), (c) => c.charCodeAt(0));

    const masterKey = await deriveMasterKey(password, salt);

    try {
        const decryptedBuffer = await window.crypto.subtle.decrypt(
            { name: "AES-GCM", iv: iv },
            masterKey,
            encryptedBuffer,
        );

        const privateKeyString = decoder.decode(decryptedBuffer);
        return JSON.parse(privateKeyString);
    } catch (error) {
        throw new Error("Password dekripsi salah atau data terkorupsi.");
    }
}

export async function encryptMessage(recipientPublicKeyJwk, plaintext) {
    const myPrivateKeyJwk = JSON.parse(localStorage.getItem("my_private_key"));
    if (!myPrivateKeyJwk) {
        throw new Error(
            "Private key pengirim tidak ditemukan di localStorage.",
        );
    }

    const myPrivateKey = await window.crypto.subtle.importKey(
        "jwk",
        myPrivateKeyJwk,
        { name: "ECDH", namedCurve: "p-256" },
        false,
        ["deriveKey"],
    );

    const recipientPublicKey = await window.crypto.subtle.importKey(
        "jwk",
        typeof recipientPublicKeyJwk === "string"
            ? JSON.parse(recipientPublicKeyJwk)
            : recipientPublicKeyJwk,
        { name: "ECDH", namedCurve: "P-256" },
        false,
        [],
    );

    const sharedKey = await window.crypto.subtle.deriveKey(
        { name: "ECDH", public: recipientPublicKey },
        myPrivateKey,
        { name: "AES-GCM", length: 256 },
        false,
        ["encrypt", "decrypt"],
    );

    const encoder = new TextEncoder();
    const iv = window.crypto.getRandomValues(new Uint8Array(12));
    const ciphertextBuffer = await window.crypto.subtle.encrypt(
        { name: "AES-GCM", iv: iv },
        sharedKey,
        encoder.encode(plaintext),
    );

    return {
        ciphertext: btoa(
            String.fromCharCode(...new Uint8Array(ciphertextBuffer)),
        ),
        iv: btoa(String.fromCharCode(...iv)),
    };
}

export async function decryptMessage(
    senderPublicKeyJwk,
    ciphertextBase64,
    ivBase64,
) {
    const myPrivateKeyJwk = JSON.parse(localStorage.getItem("my_private_key"));
    if (!myPrivateKeyJwk) {
        return "[Gagal Decrypt: Private Key Tidak Ada]";
    }

    try {
        const myPrivateKey = await window.crypto.subtle.importKey(
            "jwk",
            myPrivateKeyJwk,
            { name: "ECDH", namedCurve: "P-256" },
            false,
            ["deriveKey"],
        );

        const senderPublicKey = await window.crypto.subtle.importKey(
            "jwk",
            typeof senderPublicKeyJwk === "string"
                ? JSON.parse(senderPublicKeyJwk)
                : senderPublicKeyJwk,
            { name: "ECDH", namedCurve: "P-256" },
            false,
            [],
        );

        const sharedKey = await window.crypto.subtle.deriveKey(
            { name: "ECDH", public: senderPublicKey },
            myPrivateKey,
            { name: "AES-GCM", length: 256 },
            false,
            ["encrypt", "decrypt"],
        );

        const ciphertextBuffer = Uint8Array.from(atob(ciphertextBase64), (c) =>
            c.charCodeAt(0),
        );
        const iv = Uint8Array.from(atob(ivBase64), (c) => c.charCodeAt(0));

        const decryptedBuffer = await window.crypto.subtle.decrypt(
            { name: "AES-GCM", iv: iv },
            sharedKey,
            ciphertextBuffer,
        );

        const decoder = new TextDecoder();
        return decoder.decode(decryptedBuffer);
    } catch (error) {
        return "[Gagal Mendecrypt Pesan]";
    }
}
