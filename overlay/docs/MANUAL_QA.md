# Manual acceptance checks
1. Start the app and issue a demo token; connect in the browser.
2. Create a product at 12345 cents and confirm it displays $123.45.
3. Edit its stock, refresh, and confirm persistence.
4. Submit negative stock through the API; expect 422.
5. Create duplicate SKU for the same owner; expect 422.
6. Issue a second user's token; verify the first inventory is absent and direct access returns 403.
7. Create at least 11 records; navigate both pages and search by SKU.
8. Delete a product and confirm its GET returns 404.
9. Reload the browser; confirm a token must be supplied again.
10. Run automated tests and formatting; do not report success until they pass.
