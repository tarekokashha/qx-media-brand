# Security policy

## Reporting a vulnerability

Please **do not open a public issue** for a security problem.

Report it privately through GitHub: open the **Security** tab of this repository and choose **Report a vulnerability** (<https://github.com/tarekokashha/qx-media-brand/security/advisories/new>). That creates a private conversation with the maintainer, Tarek Okasha.

Include what you found, how to reproduce it, and the impact you expect. A short proof of concept is plenty.

## What to expect

- An acknowledgement within **7 days**.
- A fix or a clear explanation within **30 days** for confirmed issues, sooner for serious ones.
- Credit in the release notes if you would like it.

## Supported versions

Only the latest release receives fixes.

## Scope

The theme code in `qx-theme/` (output escaping, input handling, the contact-form hand-off), the build and check scripts, and the Docker development stack. The Docker stack is for local use only: it binds to 127.0.0.1 and uses throwaway credentials.

Out of scope: vulnerabilities in WordPress core, WooCommerce, third-party themes and plugins (report those to their own maintainers), and findings that need an already-compromised administrator account.
