@extends('layouts.app')

@section('content')
<div class="main-content-inner">
    <div class="guides-general-container">
        <div class="guides-general-card">
            <div class="guides-general-header">
                <h1 class="guides-general-title">KeePassXC User Guide</h1>
            </div>
            <div class="guides-general-content">
                <h2 class="guides-general-section-title">[----[1] DOWNLOADING KEEPASSXC [1]----]</h2>
                <hr class="guides-general-divider">

                <p>KeePassXC is available for both Microsoft Windows and Linux. Go to https://keepassxc.org/download to download the appropriate version for your system.</p>

                <p>Installing KeePassXC is a very simple process. Below are the installation steps for Microsoft Windows first, followed by Linux Mint (or other Debian-based Linux distributions like Ubuntu).</p>

                <h3 class="guides-general-subtitle">[----FOR MICROSOFT WINDOWS----]</h3>

                <p>First, go to "https://keepassxc.org/download/windows" and download KeePassXC for Windows. The Windows MSI installer is signed with a secure certificate from DroidMonkey Apps, LLC, as you can see in the screenshot.</p>

                <div class="guides-general-image-container">
                    <img src="{{ asset('images/guides/keepassxc/1.png') }}" alt="Windows MSI installer certificate" class="guides-general-image">
                </div>

                <p>Follow these steps to install KeePassXC on Microsoft Windows:</p>

                <ul class="guides-general-list">
                    <li>Double-click the KeePassXC-Y.Y.Y-WinZZ.msi file. Here, Y.Y.Y represents the software version, and ZZ represents the 32-bit/64-bit version of Microsoft Windows.</li>
                    <li>Click Next as shown in the screenshot below and follow the simple instructions in the installation wizard to complete the installation.</li>
                </ul>

                <div class="guides-general-image-container">
                    <img src="{{ asset('images/guides/keepassxc/2.png') }}" alt="KeePassXC installation wizard" class="guides-general-image">
                </div>
                <hr class="guides-general-divider">

                <p>Then you'll see options to choose installation location, add desktop shortcut, and start at system startup as shown in the screenshot.</p>

                <div class="guides-general-image-container">
                    <img src="{{ asset('images/guides/keepassxc/3.png') }}" alt="KeePassXC installation options" class="guides-general-image">
                </div>
                <hr class="guides-general-divider">

                <h3 class="guides-general-subtitle">[----FOR LINUX----]</h3>

                <p>You have multiple options to install KeePassXC on your Linux system. You can download it as AppImage, Flatpak, Snap, or Ubuntu PPA.</p>

                <h4 class="guides-general-subsubtitle">To download as AppImage:</h4>
                <p>Go to https://keepassxc.org/download/#linux and click the "DOWNLOAD APPIMAGE" button. Navigate to where you downloaded the AppImage and right-click on KeePassXC. Click "Properties". Then find the "Permissions" tab in the window that opens. Finally, find the "Allow executing file as program" option and make sure it's selected (should have a checkmark). Your AppImage is now ready to run. Double-click the program to run it.</p>

                <h4 class="guides-general-subsubtitle">To install with Flatpak, open Terminal and type these commands:</h4>
                <div class="guides-general-code-block">
                    <pre>flatpak remote-add --user --if-not-exists flathub https://flathub.org/repo/flathub.flatpakrepo
flatpak install --user flathub org.keepassxc.KeePassXC</pre>
                </div>
                <p class="guides-general-note">(Remember that you need to have Flatpak installed on your computer to use these commands)</p>

                <h4 class="guides-general-subsubtitle">To install with Snap, open Terminal and type:</h4>
                <div class="guides-general-code-block">
                    <pre>sudo snap install keepassxc</pre>
                </div>
                <p class="guides-general-note">(Remember that you need to have Snapd installed on your computer for this installation method)</p>

                <h4 class="guides-general-subsubtitle">Or you can download it as Ubuntu PPA. This installation method works for all Ubuntu-based operating systems. (Ubuntu, Linux Mint, Elementary OS, Zorin OS, Pop!_OS, Peppermint OS, etc.) Open your Terminal and type these commands:</h4>
                <div class="guides-general-code-block">
                    <pre>sudo add-apt-repository ppa:phoerious/keepassxc
sudo apt update
sudo apt install keepassxc</pre>
                </div>

                <h2 class="guides-general-section-title">[----[2] INTERFACE INTRODUCTION [2]----]</h2>
                <hr class="guides-general-divider">

                <h3 class="guides-general-subtitle">[----APPLICATION LAYOUT----]</h3>

                <p>The KeePassXC interface is divided into four main sections, detailed below. You can open multiple databases at once, which will appear in tabs.</p>

                <div class="guides-general-image-container">
                    <img src="{{ asset('images/guides/keepassxc/4.png') }}" alt="KeePassXC interface layout" class="guides-general-image">
                </div>
                <hr class="guides-general-divider">

                <p class="guides-general-highlight">(A) Groups – You can organize your password entries into separate groups. Groups can be nested under each other to create a hierarchy. Settings from parent groups apply to their subgroups.</p>

                <p class="guides-general-highlight">(B) Tags – Dynamic password entry groups that can be quickly viewed with a single click. Any number of custom tags can be added when editing a password. This panel also includes useful predefined searches like finding expired and weak passwords.</p>

                <p class="guides-general-highlight">(C) Entries – Entries contain all the information you want to store for a website or application in KeePassXC. This view shows all entries in the selected group. Each column can be resized, reordered, and shown/hidden according to your preference. Right-click entries to see all available options.</p>

                <p class="guides-general-highlight">(D) Preview – Shows a preview of the selected group or entry. Here you can quickly see your username and password.</p>
                <hr class="guides-general-divider">

                <h3 class="guides-general-subtitle">[----TOOLBAR----]</h3>

                <p>The toolbar provides a shortcut to open frequently used tabs related to your database.</p>

                <div class="guides-general-image-container">
                    <img src="{{ asset('images/guides/keepassxc/5.png') }}" alt="KeePassXC toolbar" class="guides-general-image">
                </div>
                <hr class="guides-general-divider">

                <p class="guides-general-highlight">(A) Database – Open Database, Save Database, Lock Database</p>
                <p class="guides-general-highlight">(B) Entries – Create Entry, Edit Entry, Delete Selected Entries</p>
                <p class="guides-general-highlight">(C) Entry Data – Copy Username, Copy Password, Copy URL, Perform Auto-Type</p>
                <p class="guides-general-highlight">(D) Tools – Password Generator, Application Settings</p>
                <p class="guides-general-highlight">(E) Search</p>

                <h2 class="guides-general-section-title">[----[3] PASSWORD GENERATOR [3]----]</h2>
                <hr class="guides-general-divider">

                <p>The password generator (dice symbol) helps you create random strong passwords and passphrases that you can use for your applications and websites you visit.</p>
                <hr class="guides-general-divider">

                <h3 class="guides-general-subtitle">[----GENERATING PASSWORDS----]</h3>

                <p>To generate random passwords, specify the characters to be used in your password (e.g., uppercase letters, numbers, special characters, etc.), and KeePassXC will randomly select characters from this set.</p>
                <p>Follow these steps to generate a random password using the Password Generator:</p>

                <ol class="guides-general-ordered-list">
                    <li>Open KeePassXC.</li>
                    <li>Go to Tools > Password Generator. The following screen will appear:</li>
                </ol>

                <div class="guides-general-image-container">
                    <img src="{{ asset('images/guides/keepassxc/6.png') }}" alt="KeePassXC password generator" class="guides-general-image">
                </div>
                <hr class="guides-general-divider">

                <ol class="guides-general-ordered-list" start="3">
                    <li>Select your desired password length by dragging the length slider.</li>
                    <li>Select the character sets you want to include in your password.</li>
                    <li>Use the regenerate button (Ctrl + R) to generate a new password using the selected options. (or click the rotation symbol)</li>
                    <li>Use the clipboard button (Ctrl + C) to copy the generated password to the clipboard. (or click the document symbol)</li>
                </ol>

                <h2 class="guides-general-section-title">[----[4] DATABASE OPERATIONS [4]----]</h2>
                <hr class="guides-general-divider">

                <h3 class="guides-general-subtitle">[----CREATING YOUR FIRST DATABASE----]</h3>

                <p>To start using KeePassXC, you first need to create a database to store your passwords and other details.</p>
                <p>Follow these steps to create a database:</p>

                <ol class="guides-general-ordered-list">
                    <li>Open your KeePassXC application. Click the Create new database button (A):</li>
                </ol>

                <div class="guides-general-image-container">
                    <img src="{{ asset('images/guides/keepassxc/7.png') }}" alt="Creating new database" class="guides-general-image">
                </div>
                <hr class="guides-general-divider">

                <ol class="guides-general-ordered-list" start="2">
                    <li>The database creation wizard appears. Enter your desired database name and a brief description (optional):</li>
                </ol>

                <div class="guides-general-image-container">
                    <img src="{{ asset('images/guides/keepassxc/8.png') }}" alt="Database name and description" class="guides-general-image">
                </div>
                <hr class="guides-general-divider">

                <ol class="guides-general-ordered-list" start="3">
                    <li>Click Continue. The Encryption Settings screen appears; we don't recommend making any changes except increasing or decreasing the decryption time using the slider. Setting the Decryption Time slider to higher values means the database will be more protected, but it will take longer to open the database.</li>
                </ol>

                <div class="guides-general-image-container">
                    <img src="{{ asset('images/guides/keepassxc/9.png') }}" alt="Encryption settings" class="guides-general-image">
                </div>
                <hr class="guides-general-divider">

                <ol class="guides-general-ordered-list" start="4">
                    <li>Click the Continue button. The Database Credentials screen appears; enter your desired database password. We recommend using a long, random password.</li>
                </ol>

                <div class="guides-general-image-container">
                    <img src="{{ asset('images/guides/keepassxc/10.png') }}" alt="Database credentials" class="guides-general-image">
                </div>
                <hr class="guides-general-divider">

                <p class="guides-general-highlight">(A) Open password generator</p>
                <p class="guides-general-highlight">(B) Toggle password visibility</p>

                <p>Keep this password for your database safe. Either memorize it or write it down somewhere. Losing the database password can cause your database to be permanently locked, and you may not be able to recover the information stored in the database.</p>

                <ol class="guides-general-ordered-list" start="5">
                    <li>Click Done. You will be prompted to choose a location to save your database file. The database file is saved to your computer with the default .kdbx extension. You can store your database anywhere you like; it is always fully encrypted to prevent unauthorized access.</li>
                </ol>
                <hr class="guides-general-divider">

                <h3 class="guides-general-subtitle">[----OPENING AN EXISTING DATABASE----]</h3>

                <p class="guides-general-content">Follow these steps to open an existing database:</p>

                <ol class="guides-general-ordered-list">
                    <li>Open your KeePassXC application. Click the Open existing database button (A) or select a database from the Recent Databases list (B).</li>
                </ol>

                <div class="guides-general-image-container">
                    <img src="{{ asset('images/guides/keepassxc/11.png') }}" alt="Opening existing database" class="guides-general-image">
                </div>
                <hr class="guides-general-divider">

                <ol class="guides-general-ordered-list" start="2">
                    <li>Navigate to your database's location on your computer and open the database file. The database unlock screen will appear:</li>
                </ol>

                <div class="guides-general-image-container">
                    <img src="{{ asset('images/guides/keepassxc/12.png') }}" alt="Database unlock" class="guides-general-image">
                </div>
                <hr class="guides-general-divider">

                <ol class="guides-general-ordered-list" start="3">
                    <li>Enter your database password.</li>
                    <li>(Optional) Locate the Key File if you selected it as an additional authentication factor when creating the database. For more information about setting up a Key File as an additional authentication factor, see the KeePassXC User Guide.</li>
                    <li>Click OK. The database opens and the following screen is displayed:</li>
                </ol>

                <div class="guides-general-image-container">
                    <img src="{{ asset('images/guides/keepassxc/13.png') }}" alt="Open database" class="guides-general-image">
                </div>
                <hr class="guides-general-divider">

                <h3 class="guides-general-subtitle">[----ADDING ENTRIES----]</h3>

                <p class="guides-general-content">All details such as usernames, passwords, URLs, attachments, notes, etc. are stored in database entries. You can create as many entries as you want in the database.</p>
                <p class="guides-general-content">Follow this step to add an entry:</p>

                <ol class="guides-general-ordered-list">
                    <li>Click Entries > Add a New Entry (plus symbol in a circle) (or press Ctrl+N). The following screen appears:</li>
                </ol>

                <div class="guides-general-image-container">
                    <img src="{{ asset('images/guides/keepassxc/14.png') }}" alt="Adding new entry" class="guides-general-image">
                </div>
                <hr class="guides-general-divider">

                <p class="guides-general-content">Enter the desired title, username, password, URL, and notes for the entry on this screen.</p>

                <ul class="guides-general-list">
                    <li>Your most frequently used usernames will automatically be available in the username dropdown menu. They will also auto-complete as you type.</li>
                    <li>You can generate a secure random password by launching the password generator by clicking the dice symbol in the password field. You can show the password by clicking the eye symbol.</li>
                    <li>After adding a URL to an entry, you can press the download button to automatically download the website's icon for this entry.</li>
                </ul>

                <ol class="guides-general-ordered-list" start="2">
                    <li>(Optional) Add tags to the entry for quick searching using the tags panel in the main database view. You can easily add new tags or select existing ones from the dropdown list.</li>
                    <li>(Optional) Select the Expires checkbox to set an expiration date for the password. You can enter the date and time manually or click the Presets button to select an expiration date and time for your password.</li>
                    <li>Click OK to add the entry to your database.</li>
                </ol>

                <h3 class="guides-general-subtitle">[----EDITING ENTRIES----]</h3>

                <p class="guides-general-content">Follow these steps to edit details in an entry:</p>

                <ol class="guides-general-ordered-list">
                    <li>Select the entry you want to edit.</li>
                    <li>Press Enter or click the edit (pencil) icon in the toolbar or right-click and select Edit Entry from the menu.</li>
                    <li>Make your desired changes.</li>
                    <li>Click OK.</li>
                </ol>

                <h3 class="guides-general-subtitle">[----DELETING ENTRIES----]</h3>

                <p class="guides-general-content">Follow these steps to delete an entry:</p>

                <ol class="guides-general-ordered-list">
                    <li>Select the entry you want to delete and press the Delete key on your keyboard.</li>
                    <li>You will be asked if you want to move the entry to the Recycle Bin (if enabled).</li>
                </ol>

                <p class="guides-general-note">You can disable the Recycle Bin from Database Settings. If the Recycle Bin is disabled, deleted entries will be permanently removed from the database.</p>

                <p class="guides-general-content">To permanently delete an entry, go to the Recycle Bin, select the entry you want to delete, and press the Delete key on your keyboard.</p>

                <h2 class="guides-general-section-title">[----[5] STORING THE DATABASE FILE [5]----]</h2>
                <hr class="guides-general-divider">

                <p class="guides-general-content">The database file you create may contain highly sensitive data and should be stored very securely. You should ensure that the database is always protected with a strong and long password. A database file protected with a strong and long password is secure and encrypted when stored on your computer or cloud storage service.</p>

                <p class="guides-general-highlight">Make sure that you or someone else doesn't accidentally delete the database file. Deleting the database file will result in complete loss of all your information (including all your passwords!) and will require a great deal of effort to manually recover your logins to various web applications.</p>

                <p class="guides-general-content">Do not share access information to your database file with anyone unless you absolutely trust them (spouse, children, etc.).</p>

                <p class="guides-general-content">You can safely store your database file in the cloud (OneDrive, Dropbox, Google Drive, Nextcloud, Syncthing, etc.). The database file is always fully encrypted; unencrypted data is never written to disk and can never be accessed by your cloud storage provider. We recommend using a storage service that keeps automatic backups (version history) of your database file in case of corruption or accidental deletion.</p>

                <h2 class="guides-general-section-title">[----[6] BACKING UP THE DATABASE FILE [6]----]</h2>
                <hr class="guides-general-divider">

                <p class="guides-general-content">It's good practice to create copies of your database file and store these copies on a different computer, smartphone, or cloud storage like Google Drive or Microsoft OneDrive. You can create backups manually using the Database → Save Database Backup… menu feature.</p>

                <div class="guides-general-image-container">
                    <img src="{{ asset('images/guides/keepassxc/15.png') }}" alt="Database backup settings" class="guides-general-image">
                </div>
                <hr class="guides-general-divider">

                <p class="guides-general-highlight">By regularly backing up your database this way, you can ensure the safety of your data and protect yourself against possible data loss.</p>
                </div>
            </div>
        </div>
    </div>

@endsection
