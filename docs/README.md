# Documentation

Devink documentation is built with [Zensical](https://zensical.org/) and managed with [uv](https://docs.astral.sh/uv/).

## Structure

```text
docs/
├── README.md
├── pyproject.toml
├── uv.lock
├── zensical.toml
└── docs/
    ├── index.md
    ├── architecture.md
    ├── api.md
    ├── database.md
    ├── security.md
    ├── operations.md
    └── decisions/
        ├── index.md
        └── ...
```

* `zensical.toml` — Zensical configuration.
* `pyproject.toml` — Python project and dependencies.
* `uv.lock` — Locked dependency versions.
* `docs/` — Documentation source files.
* `site/` — Generated documentation site.

## Requirements

Install [uv](https://docs.astral.sh/uv/getting-started/installation/) first.

Then, from the `docs/` directory, install the project dependencies:

```bash
uv sync
```

Zensical is installed automatically as a project dependency.

## Run Locally

Start the development server:

```bash
uv run zensical serve
```

The documentation will be available at:

```text
http://localhost:8000
```

To open it automatically in the browser:

```bash
uv run zensical serve --open
```

## Build

To build the documentation:

```bash
uv run zensical build
```

For a strict build:

```bash
uv run zensical build --strict
```

The generated site will be available in:

```text
site/
```

## Quick Start

```bash
cd docs

uv sync
uv run zensical serve
```
