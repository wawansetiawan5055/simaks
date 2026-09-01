#!/bin/bash

PREPEND="<?php include __DIR__ . '/partials/header.php'; ?>
<?php include __DIR__ . '/partials/sidebar.php'; ?>
<div class=\"content-wrapper\">
    <section class=\"content-header\">
        <div class=\"container-fluid\">
            <div class=\"row mb-2\">
                <div class=\"col-sm-6\">
                    <h1>LMS System</h1>
                </div>
            </div>
        </div>
    </section>
    <section class=\"content\">"

APPEND="    </section>
</div>
<?php include __DIR__ . '/partials/footer.php'; ?>"

for file in app/views/lms_*.php; do
    if ! grep -q "partials/header.php" "$file"; then
        echo "Processing $file..."
        tmp=$(mktemp)
        echo "$PREPEND" > "$tmp"
        cat "$file" >> "$tmp"
        echo "$APPEND" >> "$tmp"
        mv "$tmp" "$file"
    else
        echo "Already processed $file"
    fi
done
