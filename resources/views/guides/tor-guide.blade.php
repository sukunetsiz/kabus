@extends('layouts.app')

@section('content')
<div class="main-content-inner">
    <div class="guides-general-container">
        <div class="guides-general-card">
            <div class="guides-general-header">
                <h1 class="guides-general-title">The Tor Network</h1>
            </div>
            <div class="guides-general-content">
                <p>Tor, short for "The Onion Router," refers to a network and software system designed to provide anonymity and privacy on the internet. By encrypting user traffic and routing it through multiple servers, Tor enables individuals to conceal their identity and online activities. This system effectively prevents online tracking, circumvents censorship, and enhances security, making it a valuable tool for privacy-conscious individuals, activists, journalists, and those affected by censorship.</p>

                <p>The primary purpose of Tor is to offer users an anonymous internet experience. Typically, information such as your IP address, browser fingerprint, and browsing history is tracked and logged by various platforms while you browse online. Tor prevents this data from being collected and monitored. Once your internet traffic enters the Tor network, it passes through three different nodes: the entry node, the relay node, and the exit node. During this process, the data is protected by layered encryption, and each node knows only about the previous and next node. This structure makes tracking user identity and data extremely difficult. Additionally, Tor serves as a tool that supports freedom of expression. Many countries impose strict censorship or block access to specific internet content. Tor helps bypass these barriers, allowing individuals and communities to communicate freely. For instance, journalists and activists living under oppressive regimes can securely share information and communicate using Tor. In this way, Tor is not just a privacy tool but also a platform for freedom.</p>

                <p>Another key feature of Tor is its ability to enhance security. When your internet traffic connects to the Tor network, all your data undergoes a multilayered encryption process. This is particularly valuable when using public Wi-Fi networks or untrusted connections, as it makes it much harder for malicious actors to intercept or monitor your traffic. Furthermore, Tor users can send emails, share files, or log in to social media accounts while hiding their true identity. This feature adds a critical layer of protection against threats like online harassment, identity theft, or data breaches.</p>

                <h2 class="guides-general-section-title">Common Misconceptions About Tor</h2>
                <p>Tor is often misunderstood, leading to various misconceptions about its purpose and functionality. One of the most common misconceptions is that Tor is illegal. However, using Tor is entirely legal in most countries, and it serves as a legitimate tool for enhancing privacy and security. While some individuals may misuse Tor for illicit activities, its primary purpose is to protect user anonymity and support freedom of expression.</p>

                <p>Another misconception is that Tor makes users completely anonymous. While Tor significantly enhances privacy by hiding your IP address and encrypting your internet traffic, it does not guarantee absolute anonymity. Users can still compromise their privacy through actions like logging into personal accounts or sharing identifiable information. Additionally, using Tor alone may not be enough to ensure full protection; combining it with other privacy tools and practices is often necessary.</p>

                <p>Many also believe that Tor slows down internet speed to an unusable level. While it is true that Tor's layered encryption and routing through multiple nodes can reduce connection speeds, the impact is often manageable for most browsing activities. Users should understand that the slight trade-off in speed is a result of the robust security and privacy measures Tor provides.</p>

                <p>Lastly, some associate Tor solely with accessing the dark web. While Tor does allow access to .onion sites, it is not limited to such use. The majority of Tor users leverage the network for everyday browsing, especially in regions with heavy censorship or surveillance. Tor's mission extends far beyond the dark web, offering a vital resource for protecting online freedom and privacy.</p>

                <h2 class="guides-general-section-title">How Does Tor Work?</h2>
                <p>The Tor network ensures user anonymity by encrypting data traffic and routing it through multiple nodes. These nodes can only see specific portions of the data, playing a critical role in hiding the source and destination of the traffic. Tor's core node structure consists of three stages: the Entry Node, the Relay Node, and the Exit Node. Each node has a distinct role, and understanding these roles is essential to grasp how Tor provides anonymity.</p>

                <p>The routing process works as follows:</p>

                <h3 class="guides-general-subtitle">Entry Node</h3>
                <p>The entry node is the first point where the user connects to the Tor network. This node is the only part of the network that can see the user's real IP address. However, it does not share this information with the rest of the Tor network or the destination site. Instead, it encrypts the data and forwards it to the next node.</p>

                <div class="guides-general-highlight">
                Example: A user wants to access a news website using Tor. When the user's computer connects to the Tor network, it selects an entry node (e.g., a server located in Germany). This node knows the user's real IP address but has no knowledge that the user is trying to connect to a news website. It encrypts the traffic and anonymously forwards it to the relay node.
                </div>

                <h3 class="guides-general-subtitle">Relay Node</h3>
                <p>The relay node is an intermediate point that anonymizes and routes the data traffic. Relay nodes do not know where the data originated or where it is ultimately headed. Their sole function is to pass the data to the next node. Tor typically uses multiple relay nodes, further increasing the anonymity of the traffic.</p>

                <div class="guides-general-highlight">
                Example: After leaving the entry node in Germany, the user's traffic reaches a relay node in France. This node knows that the data came from Germany but does not identify the user. Similarly, it only knows that the data must go to the exit node, but not the final destination. Since the relay node forwards encrypted data, the user's traffic remains anonymous.
                </div>

                <h3 class="guides-general-subtitle">Exit Node</h3>
                <p>The exit node is the final point in the Tor network where the traffic exits and reaches its intended destination. This node decrypts the last layer of encryption and sends the data to its final destination on the internet. The exit node establishes a direct connection with the website or service the user wants to access.</p>

                <div class="guides-general-highlight">
                Example: After leaving the relay node in France, the user's data reaches an exit node in the United States. This exit node ensures that the traffic is delivered to the target website, such as the news site. The website only sees the traffic as coming from the United States but cannot identify the user's real IP address or the entry node.
                </div>

                <p>This multi-layered structure makes tracking the traffic and identifying its source extremely difficult. However, at the exit node, the data might not be encrypted. Therefore, users are advised to use additional encryption methods like HTTPS to enhance security.</p>

                <h2 class="guides-general-section-title">How to Download Tor Browser</h2>
                <p>To start using Tor on your computer, follow these steps to download Tor Browser:</p>

                <ul class="guides-general-list">
                <li>Visit the official Tor Project website at <a href="https://www.torproject.org/download/">https://www.torproject.org/download/</a>.</li>
                <li>Select the download option that matches your operating system:</li>
                </ul>

                <div class="guides-general-image-container">
                    <img src="{{ asset('images/guides/tor/1.png') }}" alt="Tor Download Options" class="guides-general-image">
                </div>

                <h3 class="guides-general-subtitle">Installation Instructions</h3>

                <ul class="guides-general-list">
                <li>For Windows: Double-click the .exe file you downloaded, and follow the on-screen instructions in the installer. Confirm all prompts to complete the installation.</li>
                <li>For Linux: Extract the .tar.xz file you downloaded. After extraction, navigate to the "tor-browser" folder. Inside, you'll find the Tor Browser Setup file. Simply double-click it, and the installation will begin.</li>
                </ul>

                <h3 class="guides-general-subtitle">Tor Browser Settings</h3>
                <p>For additional protection, we recommend using the pre-configured "Safest" settings in Tor Browser. First, locate the shield icon on the Tor Browser's toolbar. Click on this icon, and in the pop-up menu, select "Settings."</p>

                <div class="guides-general-image-container">
                    <img src="{{ asset('images/guides/tor/2.png') }}" alt="Tor Browser Settings" class="guides-general-image">
                </div>

                <p>This will take you to the Security Level settings page. Here, select the third option, labeled "Safest." This setting disables JavaScript, which can potentially be used to compromise your anonymity.</p>

                <div class="guides-general-image-container">
                    <img src="{{ asset('images/guides/tor/3.png') }}" alt="Tor Security Settings" class="guides-general-image">
                </div>

                <h2 class="guides-general-section-title">Privacy Recommendations for Using Tor and Monero</h2>
                <p>You don't need to have a secret to follow privacy best practices. Preserving your anonymity is always important because privacy is a human right, and no one has the right to know what you're doing on Tor or what you're buying with your Monero. Here are some key recommendations:</p>

                <div class="guides-general-highlight">
                Withdraw Monero to a Private Wallet
                If you acquire Monero from a centralized exchange, first transfer it to your personal wallet. This step helps remove any traceable links to the exchange.
                </div>

                <div class="guides-general-highlight">
                Avoid Sharing Your Main Wallet Address
                Use subaddresses for receiving payments instead of your main wallet address. Subaddresses help protect your privacy by ensuring that each transaction remains independent.
                </div>

                <div class="guides-general-highlight">
                Don't Reuse Monero Addresses
                Avoid using the same Monero address for multiple transactions to prevent linking activities together.
                </div>

                <div class="guides-general-highlight">
                Use Tor or I2P Proxy with Your Wallet
                Always connect your wallet through Tor or an I2P proxy to enhance privacy and hide your network activity.
                </div>

                <div class="guides-general-highlight">
                Avoid Personal Identifiers
                Never share your real name, surname, or any personal information on websites. ALWAYS use an email address that cannot be linked to your identity to keep your activities fully anonymous.
                </div>

                <h2 class="guides-general-section-title">About Orbot</h2>
                <p>Orbot is an app that uses the Tor network to provide a secure and private internet connection for other apps on your Android device. It acts as a proxy, allowing apps like browsers, messengers, or social media platforms to send and receive data anonymously. By routing your traffic through multiple encrypted servers, Orbot ensures that your real IP address stays hidden, giving you enhanced privacy and helping you bypass censorship.</p>

                <p>You can download Orbot directly from the Google Play Store using this link: https://play.google.com/store/apps/details?id=org.torproject.android</p>
            </div>
        </div>
    </div>
</div>
@endsection
