#!/bin/bash

# Set variables
SVN_REPO_URL="https://plugins.svn.wordpress.org/squad-modules-for-divi/"
GITHUB_REPO_URL="https://github.com/thewpsquad/squad-modules"
WORK_DIR="./../../"
LATEST_VERSION=$(svn ls $SVN_REPO_URL/tags | sort -V | tail -n 1 | sed 's/\///g')

# Create working directory
mkdir $WORK_DIR
cd $WORK_DIR

# Clone the GitHub repository
git clone $GITHUB_REPO_URL ./

# Function to copy a specific version from SVN to Git
copy_version() {
    VERSION=$1
    svn export $SVN_REPO_URL/tags/$VERSION $VERSION
    rsync -av --exclude='.svn' $VERSION/ ./
    rm -rf $VERSION
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
