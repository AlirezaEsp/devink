---
title: "ADR-002: Choose Laravel for the Backend"
description: Decision to use Laravel as the backend framework for Devink.
# icon: lucide/git-branch
# status: new
# template: index.html
# hide:
#     - navigation
#     - toc
---

# ADR-002: Choose Laravel for the Backend

* **Status:** Accepted
* **Date:** 2026-08-27
* **Decision:** Use Laravel as the backend framework for Devink.

## Context

Devink requires a backend framework capable of supporting a structured web application with authentication, authorization, content management, user interactions, background processing, and a REST API.

The framework should provide a solid foundation without forcing the project to build common application infrastructure from scratch.

We considered several backend technologies, including Laravel, Django, FastAPI, and Node.js-based frameworks.

## Decision

We will use **Laravel** as the primary backend framework for Devink.

Laravel will provide the application foundation, including:

* HTTP and routing
* Authentication and authorization
* Validation
* Database access and migrations
* Queues and background jobs
* Notifications
* File storage
* API development
* Testing infrastructure
* Application configuration

The backend will remain a single Laravel application and will expose the versioned REST API defined by the architecture.

## Rationale

### Mature Web Application Ecosystem

Devink is primarily a web application rather than a service focused solely on API performance.

Laravel provides mature solutions for the common concerns required by Devink, reducing the amount of infrastructure that needs to be designed and maintained by the project itself.

### Productive Development

Laravel provides conventions and integrated tooling that allow common backend features to be implemented consistently.

This allows development effort to remain focused on Devink's domain rather than repeatedly solving generic framework-level problems.

### Strong Database Integration

Devink is data-oriented and relies heavily on relational data.

Laravel's database layer, migrations, relationships, transactions, and ORM provide an established foundation for working with the application's relational data.

### First-Class API Support

Although Laravel is capable of server-rendered applications, it can also serve as a dedicated API backend.

This aligns with the architectural decision to keep the frontend and backend separate.

### Built-in Application Infrastructure

Devink will require capabilities such as queues, notifications, caching, file storage, authentication, and scheduled tasks.

Laravel provides first-class support for these concerns within the same ecosystem.

This reduces the need to assemble and maintain a large collection of unrelated backend components.

### Maintainability

Laravel's conventions provide a consistent structure for the backend.

A predictable framework structure makes the codebase easier to understand and maintain as the project grows and additional contributors become involved.

## Alternatives Considered

### Django

Django was considered because of its mature ecosystem, strong ORM, authentication system, and suitability for data-driven applications.

However, Laravel provides a better fit for the project's backend stack and development workflow.

### FastAPI

FastAPI was considered because of its excellent API development experience, type hints, and performance.

However, Devink requires more than an API layer. Laravel provides a broader application framework with integrated support for many of the concerns required by the project.

### Node.js Frameworks

Node.js-based frameworks were considered as another option for building the API.

They provide strong capabilities, but choosing them would introduce a different backend ecosystem without providing a significant advantage for Devink's current requirements.

### Laravel

**Selected.**

Laravel provides the required capabilities while keeping the backend development experience cohesive and avoiding unnecessary architectural complexity.

## Consequences

### Positive

* Mature ecosystem for web application development
* Integrated solutions for common backend concerns
* Strong relational database support
* Productive development workflow
* Clear conventions and project structure
* Suitable for API-driven architecture
* Good foundation for incremental growth

### Negative

* The backend becomes dependent on the Laravel ecosystem.
* Laravel's conventions may influence architectural decisions.
* Replacing Laravel in the future would require significant migration effort.
* The framework provides more functionality than a minimal API framework, which may be unnecessary for some parts of the system.

These trade-offs are accepted because the benefits align with Devink's current requirements.

## What This Decision Does Not Mean

Choosing Laravel does not mean every feature must use Laravel-specific abstractions.

The application should still maintain clear boundaries and avoid unnecessary coupling to framework internals where practical.

It also does not imply that Laravel must be used for every future service. If Devink eventually requires a separate service with different technical requirements, that decision should be evaluated independently.

## Reconsideration

This decision may be reconsidered if Laravel becomes a significant limitation for Devink, such as:

* Requirements that Laravel cannot reasonably satisfy.
* A future workload requiring a fundamentally different runtime.
* Increasing framework coupling becoming a major maintenance problem.
* A new architectural direction requiring a different backend technology.

Any major change should be documented through a new ADR.
