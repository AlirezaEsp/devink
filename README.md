# Devink

> A developer-focused publishing platform for sharing, discovering, and discussing technical content.

Devink is a publishing platform built for developers and software engineers.

It aims to provide a focused environment where developers can publish technical articles, discover high-quality content, build their professional presence, and engage with other members of the developer community.

Devink is being developed as a real-world software product, with an emphasis on maintainability, simplicity, security, and a well-defined engineering process.

## ✨ Features

Devink is currently under active development.

Planned core capabilities include:

* 📝 Technical article publishing
* 👤 Developer profiles
* 🔖 Tags and content discovery
* 💬 Comments and discussions
* ❤️ Reactions
* 🔔 Following and notifications
* 📚 Bookmarks
* 🔎 Content search
* 🛡️ Content moderation

The feature set will evolve as the product develops.

## 🏗️ Architecture

Devink follows a separated frontend/backend architecture:

```text
┌─────────────────┐
│    Frontend     │
└────────┬────────┘
         │
       REST API
         │
┌────────▼────────┐
│ Laravel Backend │
└────────┬────────┘
         │
    ┌────┴────┐
    │         │
PostgreSQL  Redis
```

The backend is built with Laravel and exposes a versioned REST API for the frontend.

For a detailed overview of the system architecture, see the [Architecture Documentation](docs/docs/architecture.md).

## 🛠️ Technology

### Backend

* PHP
* Laravel
* PostgreSQL
* Redis

### Frontend

* In development

### Infrastructure

* Docker
* Linux
* CI/CD

Technology choices and architectural decisions are documented in the [Architecture Documentation](docs/docs/architecture.md).

## 📚 Documentation

The project documentation covers the complete lifecycle of Devink, from product definition and requirements to development, deployment, and operations.

Start with the [Documentation](docs/docs/index.md).

### Main sections

* [Product](docs/docs/product.md) — Product vision, users, goals, features, and scope
* [Requirements](docs/docs/requirements.md) — Functional and non-functional requirements
* [Architecture](docs/docs/architecture.md) — System and software architecture
* [Database](docs/docs/database.md) — Database schema and design
* [API](docs/docs/api.md) — API conventions and endpoints
* [Development](docs/docs/development.md) — Local setup and development workflow
* [Security](docs/docs/security.md) — Security considerations and practices
* [Deployment](docs/docs/deployment.md) — Environments and deployment process
* [Operations](docs/docs/operations.md) — Monitoring, backups, and maintenance
* [Architecture Decisions](docs/docs/decisions/) — Important technical decisions and their rationale

## 🚧 Project Status

Devink is currently under active development.

The project is being built incrementally, starting with the core publishing experience and expanding as the product evolves.

Features, architecture, and APIs may change during development.

## 🤝 Contributing

Contributions, ideas, and discussions are welcome.

Before contributing, please read the [Contributing Guide](CONTRIBUTING.md).

## 🔐 Security

If you discover a security vulnerability, please refer to [SECURITY.md](SECURITY.md) for information about reporting it.

## 📄 License

Devink is licensed under the terms specified in [LICENSE](LICENSE).