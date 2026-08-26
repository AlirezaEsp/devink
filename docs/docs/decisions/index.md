---
title: Decisions
# description: Architectural decisions and their rationale..
icon: lucide/git-branch
# status: new
# template: index.html
# hide:
#     - navigation
#     - toc
---

# Architecture Decisions

Architecture Decision Records (ADRs) document important technical and architectural decisions made during the development of Devink.

Each decision records the context, alternatives considered, reasoning, trade-offs, and consequences of a particular architectural choice.

The purpose of ADRs is not only to document **what** was decided, but also **why** the decision was made.

## When to Create an ADR

An ADR should be created when a technical decision:

* Has a meaningful impact on the architecture.
* Has long-term consequences.
* Involves significant trade-offs.
* May be difficult for future contributors to understand without its context.
* Represents a decision that the project may need to revisit later.

Not every implementation detail requires an ADR.

## Decisions

| ID                    | Decision                      | Status   |
| --------------------- | ----------------------------- | -------- |
| [ADR-001](adr-001-separate-frontend-and-backend.md) | Separate Frontend and Backend | Accepted |


## Principles

ADRs should remain focused on decisions rather than implementation documentation.

A good ADR should make it possible for a future contributor to understand:

* What problem we were solving.
* What alternatives we considered.
* Why we chose a particular approach.
* What trade-offs we accepted.
* Under what circumstances the decision might be reconsidered.

As Devink evolves, new architectural decisions should be added to this directory rather than silently changing existing decisions.
