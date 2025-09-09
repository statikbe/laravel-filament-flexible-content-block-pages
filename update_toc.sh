#!/bin/bash

# Update table of contents for documentation files
# This script uses gh-md-toc to automatically generate and insert TOCs

echo "Updating table of contents..."

# Array of files to process
FILES=(
    "./README.md"
    "./documentation/configuration.md"
    "./documentation/extending-and-customisation.md"
    "./documentation/frontend.md"
)

# Process each file
for file in "${FILES[@]}"; do
    if [ -f "$file" ]; then
        echo "Processing $file..."
        ./gh-md-toc --insert --skip-header "$file"
    else
        echo "Warning: $file not found, skipping..."
    fi
done

echo "Cleaning up backup files..."

# Clean up backup files for each markdown file (keep only 2 most recent per file)
cleanup_backup_files() {
    echo "Cleaning up backup files..."

    # Find all markdown files that have been processed
    for md_file in "${FILES[@]}"; do
        if [ -f "$md_file" ]; then
            local base_name=$(basename "$md_file" .md)
            local dir_name=$(dirname "$md_file")

            # Clean up .orig.* files for this specific markdown file
            local orig_files=($(ls -t "$dir_name/$base_name.md.orig."* 2>/dev/null))
            if [ ${#orig_files[@]} -gt 2 ]; then
                echo "Found ${#orig_files[@]} .orig backup files for $md_file, keeping 2 most recent"
                for ((i=2; i<${#orig_files[@]}; i++)); do
                    echo "Removing ${orig_files[i]}"
                    rm "${orig_files[i]}"
                done
            fi

            # Clean up .toc.* files for this specific markdown file
            local toc_files=($(ls -t "$dir_name/$base_name.md.toc."* 2>/dev/null))
            if [ ${#toc_files[@]} -gt 2 ]; then
                echo "Found ${#toc_files[@]} .toc backup files for $md_file, keeping 2 most recent"
                for ((i=2; i<${#toc_files[@]}; i++)); do
                    echo "Removing ${toc_files[i]}"
                    rm "${toc_files[i]}"
                done
            fi
        fi
    done
}

# Clean up backup files
cleanup_backup_files

echo "Table of contents update complete!"
