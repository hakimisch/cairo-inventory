# CAIRO Inventory Management System

The **CAIRO Inventory Management System** is a professional-grade asset lifecycle platform designed to automate the tracking of **RM 7.5 million** in high-value research assets for the Centre for Artificial Intelligence and Robotics (CAIRO), UTM.

The system implements a strict multi-stage government asset lifecycle, transforming raw delivery data (**KEW.PA-1**) into permanent Capital Asset registers (**KEW.PA-3**) through an automated "Promotion" workflow.

---

## 🚀 Key Features

* **Automated Lifecycle Bridge**: Seamlessly "promotes" pending deliveries to registered assets via a secure acceptance modal that captures unit prices and locations.


* **Government Standard Reporting**: Generates pixel-perfect, print-ready PDF documents for:
* **KEW.PA-1**: Borang Penerimaan Aset Alih.


* **KEW.PA-2**: Borang Penolakan Aset Alih (Rejection Form).


* **KEW.PA-3**: Daftar Harta Modal (Parts A & B).




* **Dynamic Dashboard**: Real-time visualization of the **RM 1.57M+** current baseline dataset, including portfolio value tracking and category distribution.


* **Enterprise Security**: Robust Role-Based Access Control (RBAC) for Asset Officers and Directors, integrated with Google OAuth.



---

## 🛠️ Technical Stack

### **Core Environment**

* **Framework**: Laravel 11 (PHP 8.4).


* **Frontend**: React with Inertia.js and Tailwind CSS.


* **Database**: Amazon RDS PostgreSQL for high data integrity.



### **Production Libraries**

* **PDF Engine**: Spatie Laravel PDF (Headless Chromium/Browsershot).


* **Media Management**: Spatie Media Library with Amazon S3 storage for durable research asset photos.


* **Data Handling**: TanStack Table for high-volume filtering and sorting.



---

## ☁️ Cloud Architecture & Strategy

The system is deployed on a robust **AWS Cloud Architecture** optimized for high availability:

* **Hosting**: AWS Elastic Beanstalk (Amazon Linux 2023).


* **Networking**: Nginx with custom `.platform` routing to handle Inertia.js single-point entry.


* **Security**: Implementation of "Least Privilege" IAM policies and strict environment isolation.



---

## 🔧 Installation & Deployment Notes

### **Infrastructure Prerequisites**

To enable the PDF reporting engine on a headless AWS Linux environment, ensure the following system binaries are provisioned:

1. **Node.js & Puppeteer**: Required to drive the headless Chromium browser.


2. **Chromium Configuration**: Browsershot must be configured with `--no-sandbox` and dynamic executable paths to run within the restricted permissions of the `webapp` user.



### **Environment Setup**

Ensure the following variables are configured in your AWS Environment Properties (not a local `.env` file) to prevent credential leaks:

* `DB_CONNECTION=pgsql`
* `FILESYSTEM_DISK=s3`
* `AWS_BUCKET=your-cairo-inventory-bucket`

---

## 📅 Project Timeline & Milestones

* **March 9, 2026**: Fully operational prototype completed; **RM 1,574,476** verified baseline established.


* **March 10, 2026**: Successful migration to AWS; resolved complex Nginx routing and RDS connectivity issues.


