#!/bin/bash

# Set variables
SVN_REPO_URL="https://plugins.svn.wordpress.org/squad-modules-for-divi/"
GITHUB_REPO_URL="https://github.com/thewpsquad/squad-modules"
WORK_DIR="./../../"
LATEST_VERSION=$(svn ls $SVN_REPO_URL/tags | sort -V | tail -n 1 | sed 's/\///g')

# Create working directory
mkdir -p $WORK_DIR
cd $WORK_DIR

# Clone the GitHub repository
git clone $GITHUB_REPO_URL ./

# Function to copy a specific version from SVN to Git
copy_version() {
    VERSION=$1

    # Delete all files and folders except specified ones
    find . -mindepth 1 -maxdepth 1 \
        ! -name '.git' \
        ! -name '.github' \
        ! -name 'readme.md' \
        ! -name 'LICENSE' \
        ! -name 'CODE_OF_CONDUCT.md' \
        -exec rm -rf {} +

    svn export $SVN_REPO_URL/tags/$VERSION tags/$VERSION
    rsync -av --exclude='.svn' tags/$VERSION/ ./
    rm -rf tags/$VERSION
    git add .
    git commit -m "Import version $VERSION from SVN"
    git tag $VERSION
}

# Copy versions from 1.0.0 to latest
VERSIONS=$(svn ls $SVN_REPO_URL/tags | sort -V)
for VERSION in $VERSIONS
do
    VERSION=${VERSION%/}
    if [[ "$VERSION" > "1.0.0" || "$VERSION" == "1.0.0" ]]
    then
        echo "Copying version $VERSION"
        copy_version $VERSION
    fi
done

# Push changes to GitHub
git push origin main --tags

echo "Migration completed successfully!"
