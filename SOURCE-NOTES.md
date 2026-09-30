# Source notes

The `source/qx-theme/` folder is a reviewed text-only snapshot of the local custom WordPress theme. `source/scripts/build-hero.mjs` is the local build script for the 3D hero.

Font binaries, QX brand images, vendor libraries, the generated 3D bundle, WordPress itself, deployment configuration, and production data are excluded. The source package is useful for reviewing the implementation, but cannot be installed as a complete visual copy of the live site without those assets and a compatible WordPress setup.

Static checks found no credential assignments in the included files. PHP runtime checks were unavailable in the review environment, so execution and WordPress compatibility have not been verified here.

Marketing numbers and testimonials are content in the source. They are not independently verified by the repository.

