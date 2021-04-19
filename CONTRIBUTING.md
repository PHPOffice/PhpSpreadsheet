# Want to contribute?

If you would like to contribute, here are some notes and guidelines:

 - All new development happens on feature/fix branches, and are then merged to the `master` branch once stable; so the `master` branch is always the most up-to-date, working code
 - Tagged releases are made from the `master` branch
 - If you are going to be submitting a pull request, please fork from `master`, and submit your pull request back as a fix/feature branch referencing the GitHub issue number
 - Code style might be automatically fixed by `composer fix`
 - All code changes must be validated by `composer check`
 - [Helpful article about forking](https://help.github.com/articles/fork-a-repo/ "Forking a GitHub repository")
 - [Helpful article about pull requests](https://help.github.com/articles/using-pull-requests/ "Pull Requests")

## How to release

1. Complete CHANGELOG.md and commit
2. Create an annotated tag
    1. `git tag -a 1.2.3`
    2. Tag subject must be the version number, eg: `1.2.3`
    3. Tag body must be a copy-paste of the changelog entries
3. Push tag with `git push --tags`, GitHub Actions will create a GitHub release automatically
