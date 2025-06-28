# Command Parser

**Shell-style Command-line Parser**

- [Command Parser](#command-parser)
  - [Installation](#installation)
  - [Usage](#usage)
    - [Defining Commands](#defining-commands)
    - [Parsing Commands](#parsing-commands)

***

## Installation

Install *command-parser* via Composer:

```bash
composer require ali-eltaweel/command-parser
```

## Usage

### Defining Commands

```php
use CommandParser\Specs\{ Command, Operand, Option, OptionToken, OptionTokenType };

$git = new Command(

    name: 'git',
    description: 'Git command-line interface',
    options: [
        new Option(
            name: 'help',
            description: 'Display help information',
            isRepeatable: false,
            isFlag: true,
            tokens: [
                new OptionToken(token: 'help', type: OptionTokenType::Extended)
            ]
        )
    ],
    operands: [],
    subCommands: [
        new Command(
            name: 'commit',
            description: 'Record changes to the repository',
            options: [
                new Option(
                    name: 'message',
                    description: 'Use the given <msg> as the commit message',
                    isRepeatable: false,
                    isFlag: false,
                    tokens: [
                        new OptionToken(token: 'm', type: OptionTokenType::Short),
                        new OptionToken(token: 'message', type: OptionTokenType::Long)
                    ]
                )
            ],
            operands: [],
            subCommands: []
        ),
        new Command(
            name: 'push',
            description: 'Update remote refs along with associated objects',
            options: [
                new Option(
                    name: 'force',
                    description: 'Force update of the remote ref',
                    isRepeatable: false,
                    isFlag: true,
                    tokens: [
                        new OptionToken(token: 'f', type: OptionTokenType::Short),
                        new OptionToken(token: 'force', type: OptionTokenType::Long)
                    ]
                )
            ],
            operands: [
                new Operand(index: 0, name: 'remote', description: 'The remote repository to push to'),
                new Operand(index: 1, name: 'branch', description: 'The branch to push')
            ],
            subCommands: []
        )
    ]
);
```

### Parsing Commands

```php
use CommandParser\CommandLineParser;

$parser = new CommandLineParser();

$commandLine = $parser->parse(['git', 'commit', '-m', 'Initial commit'], $git);
$commandLine = $parser->parse(['git', 'push', 'origin', 'main'], $git);
```
