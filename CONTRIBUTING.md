# Want to contribute?

If you would like to contribute, here are some notes and guidelines:

 - All new development happens on feature/fix branches referenced with the GitHub issue number, and are then merged to the develop branch; so the develop branch is always the most up-to-date, working code
 - The master branch only contains tagged releases
 - If you are going to be submitting a pull request, please fork from develop, and submit your pull request back as a fix/feature branch referencing the GitHub issue number
 - Code changes must be validated by PHP-CS-Fixer and PHP_CodeSniffer (via `./vendor/bin/php-cs-fixer fix --verbose && ./vendor/bin/phpcs samples/ src/ tests/ --standard=PSR2 -n`)
 - [Helpful article about forking](https://help.github.com/articles/fork-a-repo/ "Forking a GitHub repository")
 - [Helpful article about pull requests](https://help.github.com/articles/using-pull-requests/ "Pull Requests")
