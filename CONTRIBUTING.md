# Contributing Guidelines

Welcome to our project's contributing guidelines! We appreciate your interest in contributing to our internal project. To maintain a collaborative and efficient development process, we've put together a set of guidelines to help streamline contributions from our team members. Please take a moment to familiarize yourself with these guidelines to ensure a smooth and productive experience for everyone involved.

## Issue Creation

1. For each new feature or bug fix, please create an [issue](https://github.com/acecconato/AnthonyCecconato_8_21072023/issues/new/choose) in the repository.
2. Assign the relevant labels, including at least: 
   - Effort
   - Priority
   - Complexity

## Getting Started

1. Clone the repository from the `main` branch
2. Develop your feature in a separate branch.

## Code Quality Standards

Ensure that your code meets our quality standards by running the following command: `make qa`

This command will execute the following checks:
- lint:twig templates
- lint:yaml
- composer-valid  
- container
- doctrine schema validaton
- phpstan level 9
- stylelint
- phpunit tests
- php-cs-fixer fix

## PHPUnit Tests

1. Write PHPUnit tests to cover your code changes.

2. Ensure a minimum code coverage of 80%.

3. You can check code coverage by running the following command: `make phpunit-coverage`

## Pull Request Submission

1. Submit a pull request (PR) for your feature or bug fix.

2. The developer responsible for CI/CD will review and validate your PR.

3. Once your PR is approved, it will be merged into the `main` branch.
