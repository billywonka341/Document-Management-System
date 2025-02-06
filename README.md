# Document Management System

A web-based application designed to efficiently manage, store, and retrieve documents, ensuring secure access and streamlined workflows.

## Table of Contents

- [Features](#features)
- [Why This Project is Beneficial](#why-this-project-is-beneficial)
- [Technologies Used](#technologies-used)
- [Installation](#installation)
- [Usage](#usage)
- [Contributing](#contributing)
- [License](#license)
- [Contact](#contact)

## Features

- **User Authentication via Access Code**: Secure login for allowed emails.
- **Document Upload**: Upload and store documents with metadata (only authorized emails).
- **Document Deletion**: Ability to delete documents (only authorized emails).
- **Document Retrieval**: Search and retrieve documents.
- **Download Document**: Ability to download documents.
- **View Document**: Ability to view documents.
- **Dark Mode Feature**: Dark Mode Feature.
- **Access Control**: Manage allowed users to perform delete and upload operations.
- **Audit Trail**: Track document access, date, file size, and file type.

## Why This Project is Beneficial

Implementing a Document Management System (DMS) offers several advantages:

1. **Enhanced Efficiency and Time Savings**: Automating tasks such as searching, storing, and sharing documents reduces time spent on manual processes, allowing employees to focus on more productive activities.

2. **Improved Security and Accessibility**: A DMS provides robust security features, including encryption and access management, ensuring that sensitive information is protected and only accessible to authorized personnel.

3. **Cost Savings**: Transitioning to a digital document management system reduces the need for physical storage space and associated costs, leading to significant savings over time.

4. **Enhanced Collaboration**: With centralized digital storage, team members can access and work on documents simultaneously, improving collaboration and reducing delays.

5. **Environmental Benefits**: Reducing paper usage through digital document management contributes to environmental sustainability efforts.

##ScreenShots:

**Login via Email Access Code**
![image](https://github.com/user-attachments/assets/27f61e02-63dd-489b-acab-8b45c0c75e43)

**Code Sent Verify Code**
![image](https://github.com/user-attachments/assets/473505bb-1fef-4b9a-9ba6-d99b918c92a8)

**Dashboard**
![image](https://github.com/user-attachments/assets/99f6f021-1b17-4533-b03f-7e36c514a60b)

**Dashboard with File**
![image](https://github.com/user-attachments/assets/24dbe8a8-b36f-4814-8718-263832168163)

**View All Documents**
![image](https://github.com/user-attachments/assets/25198438-103d-4c56-b6d2-9615d95179db)

**Upload Document**
![image](https://github.com/user-attachments/assets/4f47cb90-858f-46ea-a90a-1cc8c7e2473d)

**Delete Document**
![image](https://github.com/user-attachments/assets/135767d7-1316-4044-aebe-8668d41b31d9)





## Technologies Used

- **Frontend**: HTML, CSS, JavaScript
- **Backend**: PHP
- **Database**: MySQL (Not in Use)
- **Libraries**: PHPMailer for email functionalities

## Installation

1. **Clone the Repository**:
   ```bash
   git clone https://github.com/billywonka341/Document-Management-System.git
   cd Document-Management-System
   

2. **Set Up the Database (Not Required)**:
   - Create a MySQL database named `document_management`.
   - Import the provided `database.sql` file to set up the necessary tables:
     ```bash
     mysql -u your_username -p document_management < database.sql
     ```

3. **Configure the Application (Only PHP Required)**:
   - Rename `config.php.example` to `config.php`.
   - Update the database connection settings in `config.php` (Not Required):
     ```php
     define('DB_SERVER', 'localhost');
     define('DB_USERNAME', 'your_username');
     define('DB_PASSWORD', 'your_password');
     define('DB_NAME', 'document_management');
     ```
   - Configure email settings for PHPMailer in `config.php` (Must):
     ```php
     define('EMAIL_HOST', 'your_smtp_host');
     define('EMAIL_USERNAME', 'your_email@example.com');
     define('EMAIL_PASSWORD', 'your_email_password');
     define('EMAIL_PORT', 587); // or 465 depending on your SMTP server
     ```

4. **Install Dependencies**:
   - Ensure you have Composer installed. (Optional)
   - Navigate to the project directory and run:
     ```bash
     composer install
     ```

5. **Start the Application**:
   - Deploy the application on a local or remote server with PHP support.
   - Access the application via `http://your-server-address/index.php`.

## Usage

- **Register**: Add a User Email in Allowed Email in Config.php.
- **Login**: Access your account using your email.
- **Upload Document**: Navigate to the upload section to add new documents.
- **View Documents**: Browse and search for documents in the repository.
- **Manage Access**: Admin users can assign permissions to different users.

## Contributing

We welcome contributions to enhance the functionality and features of this project. To contribute:

1. **Fork the Repository**: Click on the 'Fork' button at the top right of this page.
2. **Clone Your Fork**:
   ```bash
   git clone https://github.com/billywonka341/Document-Management-System.git
   cd Document-Management-System
   ```
3. **Create a New Branch**:
   ```bash
   git checkout -b feature/your-feature-name
   ```
4. **Make Your Changes**: Implement your feature or fix.
5. **Commit Your Changes**:
   ```bash
   git commit -m "Description of your changes"
   ```
6. **Push to Your Fork**:
   ```bash
   git push origin feature/your-feature-name
   ```
7. **Submit a Pull Request**: Navigate to the original repository and click on 'New Pull Request'.

## License

This project is licensed under the MIT License. See the [LICENSE](LICENSE) file for details.

## Contact

For questions, suggestions, or feedback, please open an issue in this repository or contact the project maintainer at [your-email@example.com].
```

**Notes**:

- Replace placeholder values (e.g., `your_username`, `your_password`, `your_email@example.com`) with actual values or instructions for users to input their own.
- Regularly update the `README.md` to reflect any changes or additions to the project.
