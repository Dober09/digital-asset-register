# 🏢 Digital Asset Register System (DARS)

A mobile-friendly web application that automates the registration, tracking, and verification of assets entering and leaving a building — replacing the current paper-based process.

Built with **PHP + MySQL**, designed for smartphones first.

---

## 🎯 Project Overview

The Digital Asset Register System (DARS) allows security personnel and staff to:

- Register assets **entering** the building (check-in)
- Register assets **leaving** the building (check-out)
- Capture asset details using **smartphones**
- Generate and scan **QR codes** for fast asset lookup
- **Verify** asset ownership/authorization
- Produce **reports** for audits and management
- Enforce **security controls** (authentication, roles, audit logs)

> 📌 This project is part of an academic/team development exercise. All team members must read the docs in `/docs` before writing code.

---

## 👥 Team Setup (Getting Started)

### 1. Prerequisites
Install the following on your machine:

| Tool | Version | Purpose |
|------|---------|---------|
| PHP | 8.1+ | Backend |
| MySQL / MariaDB | 8.0+ / 10.4+ | Database |
| Composer | Latest | PHP dependency manager |
| Git | Latest | Version control |
| XAMPP / Laragon / MAMP | Latest | Local dev stack (easiest) |
| VS Code | Latest | Recommended editor |
| Ngrok (optional) | Latest | Expose local server to phone for mobile testing |

### 2. Clone the Repository
```bash
git clone https://github.com/<your-org>/digital-asset-register.git
cd digital-asset-register