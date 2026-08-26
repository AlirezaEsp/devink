---
# title: Overview
# description: General overview of of Devink.
icon: lucide/home
# status: new
# template: index.html
# hide:
#     - navigation
#     - toc
---


# Devink

> A developer-focused publishing platform for sharing, discovering, and discussing technical content.

## Introduction

Devink is a publishing platform built specifically for developers and software engineers.

The goal of Devink is to provide a focused environment for creating, publishing, discovering, and engaging with technical content — with an emphasis on software development and engineering.

Devink is designed as a real-world software product rather than a demonstration or learning project. The project follows a structured engineering process covering product definition, system design, development, testing, security, deployment, and operations.

---

## Project Status

Devink is currently under active development.

The project is being built incrementally, with product requirements, architecture, API contracts, and implementation evolving together throughout the development lifecycle.

---

## Documentation

This documentation covers the different aspects of Devink, from product requirements and architecture to development and deployment.

### Product

Understand what Devink is, who it is built for, and what problems it aims to solve.

- [Product Overview](product.md)
- [Requirements](requirements.md)

### Architecture

Learn how Devink is structured and how its major components interact.

- [Architecture](architecture.md)
- [Database](database.md)
- [API](api.md)

### Development

Everything developers need to set up, develop, test, and contribute to Devink.

- [Development Guide](development.md)

### Security & Operations

Documentation related to securing, deploying, monitoring, and maintaining Devink.

- [Security](security.md)
- [Deployment](deployment.md)
- [Operations](operations.md)

### Architecture Decisions

Important technical and architectural decisions made throughout the project are documented as Architecture Decision Records.

- [Architecture Decision Records](decisions/)

---

## Technology

Devink is built using a modern web application architecture.

### Backend

- Laravel
- PHP
- PostgreSQL
- Redis

### Frontend

- [To be defined]

### Infrastructure

- Docker
- Linux
- CI/CD

The exact technology choices and architectural decisions are documented in the [Architecture](architecture.md) section.

---

## Project Principles

Devink is developed around a few core principles:

### Simplicity

Prefer simple solutions over unnecessary complexity.

### Maintainability

Code and architecture should remain understandable and maintainable as the project grows.

### Documentation

Important decisions, requirements, and technical knowledge should be documented alongside the project.

### Security

Security is considered throughout the development lifecycle rather than as a final step before release.

### Quality

Features should be tested and reviewed before being considered complete.

### Incremental Development

Devink is built incrementally. New complexity is introduced when the product actually requires it.
