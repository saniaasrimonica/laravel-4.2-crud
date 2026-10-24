# Start here

This ZIP is a project generator plus application source overlay. It does not bundle Laravel framework/vendor files. The setup script downloads the official Laravel 13 skeleton and installs dependencies, copies the supplied source, runs migrations and tests. Internet access and PHP 8.3+/Composer are required on your computer.

```bash
bash setup.sh inventory-app
cd inventory-app
vendor/bin/pint
php artisan test
php artisan inventory:token your-email@example.com
php artisan serve
```

If a step fails, fix the reported error before continuing. The script refuses to overwrite an existing destination. Format before uploading so the CI formatting check passes.

## Upload manually to GitHub
1. Generate `inventory-app` using the command above.
2. Upload the CONTENTS of `inventory-app` to the repository root using Add file → Upload files. Include app, bootstrap, config, public, resources, routes, tests, database migrations/factories, artisan, composer.json, composer.lock, phpunit.xml and docs. Include the CI workflow through GitHub's Create new file if your file picker hides `.github`.
3. Never upload .env, vendor, node_modules, database.sqlite or logs. Keep the generated .gitignore.
4. Commit message: `Rebuild inventory CRUD on Laravel 13 with owner-scoped API and tests`.
5. Optional repository name: `laravel-inventory-api`. Description: `Laravel product inventory API with Sanctum authentication, ownership policies, validated CRUD, pagination, Docker development and automated tests.`

Uploading just the overlay is not a runnable Laravel repository. Run setup first, then upload the generated app. If you cannot use a terminal now, keep this package until PHP/Composer are available.

Senior engineering is demonstrated by correct behavior, thoughtful trade-offs and maintainable code—not file count. Treat this as a project to understand, run and extend; it does not itself establish years of professional experience.
