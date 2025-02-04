@extends('layouts.app')

@section('content')
<div class="main-content-inner">
    <div class="guides-general-container">
        <div class="guides-general-card">
            <div class="guides-general-header">
                <h1 class="guides-general-title">Kleopatra User Guide</h1>
            </div>
            <div class="guides-general-content">
                <!-- Introduction -->
                <h2 class="guides-general-section-title">[----[1] UNDERSTANDING PGP ENCRYPTION AND KLEOPATRA [1]----]</h2>
                <hr class="guides-general-divider">
                <p>
                    PGP (Pretty Good Privacy) represents one of the most trusted and reliable methods for securing digital communications. Although the original proprietary PGP software is no longer maintained, its open-source successor OpenPGP remains a cornerstone of digital privacy. By using public key cryptography, PGP allows you to communicate securely, verify message authenticity, and protect sensitive files from unauthorized access.
                </p>
                <h3 class="guides-general-subtitle">What is PGP Encryption?</h3>
                <p>
                    PGP encryption uses a system of asymmetric cryptography, where each user holds a public key (which can be shared) and a private key (which must remain confidential). When encrypting a message, PGP applies multiple layers of protection:
                </p>
                <ul class="guides-general-list">
                    <li>The message is compressed to speed up transmission and enhance security.</li>
                    <li>A random session key is generated for symmetric encryption.</li>
                    <li>The session key is encrypted using the recipient’s public key.</li>
                    <li>Both the encrypted message and session key are transmitted together.</li>
                </ul>
                <h3 class="guides-general-subtitle">The Core Functions of PGP</h3>
                <ol class="guides-general-ordered-list">
                    <li><strong>Message Encryption</strong>
                        <ul class="guides-general-list">
                            <li>Ensures that only intended recipients can read the message</li>
                            <li>Protects against surveillance and interception</li>
                            <li>Maintains confidentiality during transmission</li>
                        </ul>
                    </li>
                    <li><strong>Digital Signatures</strong>
                        <ul class="guides-general-list">
                            <li>Verifies the sender's identity</li>
                            <li>Ensures message integrity</li>
                            <li>Prevents tampering</li>
                            <li>Provides non-repudiation</li>
                        </ul>
                    </li>
                    <li><strong>File Protection</strong>
                        <ul class="guides-general-list">
                            <li>Secures sensitive documents</li>
                            <li>Enables safe file sharing</li>
                            <li>Protects against unauthorized access</li>
                        </ul>
                    </li>
                </ol>
                <hr class="guides-general-divider">

                <!-- Installing Kleopatra -->
                <h2 class="guides-general-section-title">[----[2] INSTALLING KLEOPATRA AND GETTING STARTED [2]----]</h2>
                <hr class="guides-general-divider">
                <p>
                    Kleopatra is a certificate manager and graphical user interface (GUI) for GnuPG. It simplifies your PGP operations. The installation process varies depending on your operating system.
                </p>
                <h3 class="guides-general-subtitle">Windows Installation</h3>
                <ol class="guides-general-ordered-list">
                    <li>Download Gpg4win from the official website. (https://www.gpg4win.org/)</li>
                    <li>Run the installer with administrative privileges.</li>
                    <li>Make sure Kleopatra is selected during the component selection.</li>
                    <li>Complete the installation process.</li>
                    <li>Launch Kleopatra to begin your setup.</li>
                </ol>
                <h3 class="guides-general-subtitle">Linux Installation</h3>
                <ol class="guides-general-ordered-list">
                    <li>Open your terminal.</li>
                    <li>For Ubuntu/Debian systems, run:
                        <div class="guides-general-code-block">
                            <pre>sudo apt-get update
sudo apt-get install kleopatra</pre>
                        </div>
                    </li>
                </ol>
                <div class="guides-general-image-container">
                    <img src="{{ asset('images/guides/kleopatra/1.png') }}" alt="Kleopatra main view" class="guides-general-image">
                </div>
                <hr class="guides-general-divider">

                <!-- Creating Your First Key Pair -->
                <h2 class="guides-general-section-title">[----[3] CREATING YOUR FIRST KEY PAIR [3]----]</h2>
                <hr class="guides-general-divider">
                <h3 class="guides-general-subtitle">Step-by-Step Key Generation</h3>
                <ol class="guides-general-ordered-list">
                    <li>Launch Kleopatra.</li>
                    <li>Click the "New Key Pair" button or navigate to File → New OpenPGP Key Pair.</li>
                    <li>Enter your identity information:
                        <ul class="guides-general-list">
                            <li>Full Name (use a pseudonym if privacy is crucial)</li>
                            <li>Email Address (consider using a dedicated email)</li>
                            <li>Optional Comment (to help identify the key’s purpose)</li>
                        </ul>
                    </li>
                </ol>
                <div class="guides-general-image-container">
                    <img src="{{ asset('images/guides/kleopatra/2.png') }}" alt="Key creation page" class="guides-general-image">
                </div>
                <hr class="guides-general-divider">
                <h3 class="guides-general-subtitle">Securing Your Private Key</h3>
                <ol class="guides-general-ordered-list">
                    <li>Create a strong passphrase:
                        <ul class="guides-general-list">
                            <li>Mix uppercase and lowercase letters</li>
                            <li>Include numbers and special characters</li>
                            <li>Make it memorable but complex</li>
                            <li>Consider using a password manager</li>
                        </ul>
                    </li>
                    <li>Store your private key securely:
                        <ul class="guides-general-list">
                            <li>Use encrypted storage or offline backups</li>
                            <li>Never share or expose your private key</li>
                        </ul>
                    </li>
                </ol>
                <hr class="guides-general-divider">

                <!-- Basic Operations with Kleopatra -->
                <h2 class="guides-general-section-title">[----[4] BASIC OPERATIONS WITH KLEOPATRA [4]----]</h2>
                <hr class="guides-general-divider">
                <h3 class="guides-general-subtitle">Managing Public Keys</h3>
                <ol class="guides-general-ordered-list">
                    <li>
                        <strong>Importing Keys:</strong>
                        <ul class="guides-general-list">
                            <li>Click "Import" on the main interface.</li>
                            <li>Select the key file or paste the key text.</li>
                            <li>Verify the key fingerprint.</li>
                        </ul>
                    </li>
                    <li>
                        <strong>Sharing Your Public Key:</strong>
                        <ul class="guides-general-list">
                            <li>Select your key pair.</li>
                            <li>Click "Export" and choose the ASCII armor format.</li>
                            <li>Save or copy the key text.</li>
                        </ul>
                    </li>
                </ol>
                <div class="guides-general-image-container">
                    <img src="{{ asset('images/guides/kleopatra/3.png') }}" alt="Importing PGP Public Key" class="guides-general-image">
                </div>
                <div class="guides-general-image-container">
                    <img src="{{ asset('images/guides/kleopatra/4.png') }}" alt="Exporting PGP Public Key" class="guides-general-image">
                </div>
                <hr class="guides-general-divider">
                <h3 class="guides-general-subtitle">Encrypting Messages</h3>
                <ol class="guides-general-ordered-list">
                    <li>Open Kleopatra’s Notepad.</li>
                    <li>Type or paste your message.</li>
                </ol>
                <div class="guides-general-image-container">
                    <img src="{{ asset('images/guides/kleopatra/5.png') }}" alt="Notepad tab" class="guides-general-image">
                </div>
                <hr class="guides-general-divider">
                <ol class="guides-general-ordered-list" start="3">
                    <li>Click the "Recipients" tab.</li>
                    <li>Select encryption options:
                        <ul class="guides-general-list">
                            <li>"Encrypt for me" (to retain your ability to read the message)</li>
                            <li>"Encrypt for others" (choose recipients)</li>
                            <li>"Sign as" (to add your digital signature)</li>
                        </ul>
                    </li>
                </ol>
                <div class="guides-general-image-container">
                    <img src="{{ asset('images/guides/kleopatra/6.png') }}" alt="Recipients tab" class="guides-general-image">
                </div>
                <hr class="guides-general-divider">
                <ol class="guides-general-ordered-list" start="5">
                    <li>Click "Sign/Encrypt Notepad" to process your message.</li>
                </ol>
                <div class="guides-general-image-container">
                    <img src="{{ asset('images/guides/kleopatra/7.png') }}" alt="Encrypted message" class="guides-general-image">
                </div>
                <hr class="guides-general-divider">
                <h3 class="guides-general-subtitle">Decrypting Messages</h3>
                <ol class="guides-general-ordered-list">
                    <li>Open Kleopatra’s Notepad and paste the encrypted message.</li>
                    <li>Click "Decrypt/Verify".</li>
                    <li>Enter your passphrase when prompted.</li>
                    <li>Review the decrypted content.</li>
                </ol>
                <div class="guides-general-image-container">
                    <img src="{{ asset('images/guides/kleopatra/8.png') }}" alt="Decrypted message" class="guides-general-image">
                </div>
                <hr class="guides-general-divider">

                <!-- Advanced Features and Usage -->
                <h2 class="guides-general-section-title">[----[5] ADVANCED FEATURES AND USAGE [5]----]</h2>
                <hr class="guides-general-divider">
                <h3 class="guides-general-subtitle">File Encryption</h3>
                <ol class="guides-general-ordered-list">
                    <li>Select "Sign/Encrypt" and choose the file you wish to protect.</li>
                    <li>Choose the intended recipients.</li>
                    <li>Add your signature if desired.</li>
                    <li>Save the encrypted file.</li>
                </ol>
                <div class="guides-general-image-container">
                    <img src="{{ asset('images/guides/kleopatra/9.png') }}" alt="File selection/folders" class="guides-general-image">
                </div>
                <div class="guides-general-image-container">
                    <img src="{{ asset('images/guides/kleopatra/10.png') }}" alt="File encryption process" class="guides-general-image">
                </div>
                <hr class="guides-general-divider">
                <h3 class="guides-general-subtitle">Creating Signed Messages</h3>
                <ol class="guides-general-ordered-list">
                    <li>Compose your message.</li>
                    <li>Select "Sign as" without choosing encryption.</li>
                    <li>Pick your signing key.</li>
                    <li>Generate the signed message.</li>
                </ol>
                <div class="guides-general-image-container">
                    <img src="{{ asset('images/guides/kleopatra/11.png') }}" alt="Writing a message" class="guides-general-image">
                </div>
                <div class="guides-general-image-container">
                    <img src="{{ asset('images/guides/kleopatra/12.png') }}" alt="Signing the message" class="guides-general-image">
                </div>
                <div class="guides-general-image-container">
                    <img src="{{ asset('images/guides/kleopatra/13.png') }}" alt="Signed message" class="guides-general-image">
                </div>
                <hr class="guides-general-divider">
                <h3 class="guides-general-subtitle">Private Key Protection</h3>
                <p>
                    Always keep your private key secure. Never share it, use strong passphrases, maintain secure offline backups, consider hardware security keys, and update your keys regularly.
                </p>
                <hr class="guides-general-divider">

                <!-- Privacy Recommendations -->
                <h2 class="guides-general-section-title">[----[6] PRIVACY RECOMMENDATIONS [6]----]</h2>
                <hr class="guides-general-divider">
                <ol class="guides-general-ordered-list">
                    <li>
                        <strong>Use Separate Key Pairs</strong>
                        <ul class="guides-general-list">
                            <li>Create different keys for various purposes.</li>
                            <li>Keep your identities separated.</li>
                            <li>Rotate keys periodically.</li>
                        </ul>
                    </li>
                    <li>
                        <strong>Secure Communication Channels</strong>
                        <ul class="guides-general-list">
                            <li>Exchange keys over encrypted channels.</li>
                            <li>Always verify recipient identities.</li>
                            <li>Use secure deletion practices for sensitive data.</li>
                        </ul>
                    </li>
                </ol>
                <p class="guides-general-highlight">
                    Remember: Security is an ongoing process. Stay updated on best practices and protect your private keys at all times.
                </p>
            </div>
        </div>
    </div>
</div>
@endsection

