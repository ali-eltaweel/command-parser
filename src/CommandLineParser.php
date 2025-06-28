<?php

namespace CommandParser;

/**
 * Commandline Parser.
 * 
 * @api
 * @final
 * @since 0.1.0
 * @version 1.1.0
 * @package command-parser
 * @author Ali M. Kamel <ali.kamel.dev@gmail.com>
 */
final class CommandLineParser {

    /**
     * Whether to treat all tokens as operands after encountering a '--' token.
     * 
     * @internal
     * @since 1.0.0
     * @var bool $treatAllTokensAsOperands
     */
    private bool $treatAllTokensAsOperands;
    
    /**
     * The specification of the last parsed standard option missing its argument.
     * 
     * @internal
     * @since 1.0.0
     * @var ?Specs\Option $incompleteOption
     */
    private ?Specs\Option $incompleteOption;

    /**
     * The token of the last parsed standard option missing its argument.
     * 
     * @internal
     * @since 1.0.0
     * @var ?Specs\OptionToken $incompleteOptionToken
     */
    private ?Specs\OptionToken $incompleteOptionToken;

    /**
     * The options parsed from the command line.
     * 
     * @internal
     * @since 1.0.0
     * @var array<string, Option> $options
     */
    private array $options;

    /**
     * The operands parsed from the command line.
     * 
     * @internal
     * @since 1.0.0
     * @var array<Operand> $operands
     */
    private array $operands;

    /**
     * Whether the parser can accept sub-commands.
     * Starting with true, set to false with the first operand.
     * 
     * @internal
     * @since 1.0.0
     * @var bool $canAcceptSubCommands
     */
    private bool $canAcceptSubCommands;
    
    /**
     * The sub-commands parsed from the command line.
     * 
     * @internal
     * @since 1.0.0
     * @var Command[] $subCommands
     */
    private array $subCommands;

    /**
     * Resets the parser state.
     * 
     * @api
     * @final
     * @since 1.0.0
     * @version 1.0.0
     * 
     * @return void
     */
    public final function reset () {

        $this->treatAllTokensAsOperands = false;
        $this->incompleteOptionToken    = null;
        $this->incompleteOption         = null;
        $this->options                  = [];
        $this->operands                 = [];
        $this->canAcceptSubCommands     = true;
        $this->subCommands              = [];
    }

    /**
     * Parses the command line arguments into a Command object.
     * 
     * @api
     * @final
     * @since 1.0.0
     * @version 1.1.0
     * 
     * @param array<string> $commandLine  The command line arguments.
     * @param Specs\Command $commandSpecs  The specifications of the command to parse.
     * 
     * @return ?Command Returns a Command object or null if the command line is empty.
     * 
     * @throws Exceptions\InvalidCommandSpecsException If the command name does not match the specs.
     * @throws Exceptions\OptionNotFoundException If an option is not found in the command specs.
     * @throws Exceptions\MissingOptionArgumentException If an option argument is missing.
     * @throws Exceptions\OptionRepetitionDeniedException If an option is repeated but not allowed.
     * @throws Exceptions\MissingRequiredOptionException If a required option is missing.
     * @throws Exceptions\OptionTokenAlreadyDefinedException If an option token is defined more than once in the command specs.
     * @throws Exceptions\MissingRequiredOperandException If a required operand is missing.
     */
    public final function parse(array $commandLine, Specs\Command $commandSpecs): ?Command {

        $this->reset();

        $this->commandSpecs = $commandSpecs;

        if (empty($commandLine)) {
            
            return null;
        }
        
        $commandName = array_shift($commandLine);

        if ($commandName != $commandSpecs->name) {

            throw new Exceptions\InvalidCommandSpecsException('Invalid command name: ' . $commandName);
        }

        $this->validateCommandSpecs($commandSpecs);

        foreach ($commandLine as $i => $token) {

            if (empty($token)) {

                continue;
            }

            if ($this->treatAllTokensAsOperands) {

                $this->parseOperand($token, $commandSpecs);
                continue;
            }

            if ($token == '--') {
                
                $this->treatAllTokensAsOperands = true;
                continue;
            }

            if (!is_null($this->incompleteOption)) {

                $this->addOption($this->incompleteOption, $token);
                $this->incompleteOption      = null;
                $this->incompleteOptionToken = null;
                continue;
            }

            if (str_starts_with($token, '--')) {

                $this->parseExtendedOption($token, $commandSpecs);
                continue;
            }

            if (str_starts_with($token, '-')) {

                $this->parseStandardOption($token, $commandSpecs);
                continue;
            }

            if ($this->canAcceptSubCommands && $commandSpecs->hasSubCommand($token)) {

                $this->parseSubCommand(
                    commandLine:  array_slice($commandLine, $i),
                    commandSpecs: $commandSpecs->getSubCommand($token),
                    mainCommandSPecs: $commandSpecs
                );

                break;
            }

            $this->parseOperand($token, $commandSpecs);
        }

        if (!is_null($this->incompleteOption)) {

            throw new Exceptions\MissingOptionArgumentException($commandSpecs->name, "-{$this->incompleteOptionToken->token}");
        }

        $command = new Command(

            name: $commandSpecs->name,
            options: array_values($this->options),
            operands: $this->operands,
            subCommands: $this->subCommands
        );

        $this->validateCommand($command, $commandSpecs);

        return $command;
    }

    /**
     * Parses a sub-command from the command line.
     * 
     * @internal
     * @since 1.1.0
     * @version 1.0.0
     * 
     * @param string[] $commandLine
     * @param Specs\Command $commandSpecs
     * @param Specs\Command $mainCommandSPecs
     * 
     * @throws Exceptions\OptionNotFoundException
     * @throws Exceptions\MissingOptionArgumentException
     * @throws Exceptions\OptionRepetitionDeniedException
     * @throws Exceptions\MissingRequiredOptionException
     * @throws Exceptions\OptionTokenAlreadyDefinedException
     * @throws Exceptions\MissingRequiredOperandException
     * @return void
     */
    private function parseSubCommand(array $commandLine, Specs\Command $commandSpecs, Specs\Command $mainCommandSPecs): void {

        try {

            $this->subCommands[] = (new static)->parse($commandLine, $commandSpecs);
            
        } catch (Exceptions\OptionNotFoundException $e) {

            throw new Exceptions\OptionNotFoundException($e->option, "{$mainCommandSPecs->name} {$e->command}");

        } catch (Exceptions\MissingOptionArgumentException $e) {

            throw new Exceptions\MissingOptionArgumentException("{$mainCommandSPecs->name} {$e->command}", $e->optionToken);
            
        } catch (Exceptions\OptionRepetitionDeniedException $e) {

            throw new Exceptions\OptionRepetitionDeniedException("{$mainCommandSPecs->name} {$e->command}", $e->optionName);
        
        } catch (Exceptions\MissingRequiredOptionException $e) {

            throw new Exceptions\MissingRequiredOptionException("{$mainCommandSPecs->name} {$e->command}", $e->optionName);

        } catch (Exceptions\OptionTokenAlreadyDefinedException $e) {

            throw new Exceptions\OptionTokenAlreadyDefinedException(
                token: $e->token,
                option: $e->option,
                commandName: "{$mainCommandSPecs->name} {$e->commandName}"
            );
        } catch (Exceptions\MissingRequiredOperandException $e) {

            throw new Exceptions\MissingRequiredOperandException("{$mainCommandSPecs->name} {$e->command}", $e->operand);
        }
    }

    /**
     * Parses a standard option token.
     * 
     * @internal
     * @since 1.0.0
     * @version 1.0.0
     * 
     * @param string        $token        The token to parse.
     * @param Specs\Command $commandSpecs The command specifications.
     * 
     * @return void
     * 
     * @throws Exceptions\OptionNotFoundException If the option is not found in the command specs.
     * @throws Exceptions\OptionRepetitionDeniedException If the option is repeated but not allowed.
     */
    private function parseStandardOption(string $token, Specs\Command $commandSpecs): void {

        foreach (str_split(substr($token, 1)) as $i => $c) {

            if (is_null($optionSpecs = $commandSpecs->getOption(token: $c, tokenType: Specs\OptionTokenType::Standard))) {

                throw new Exceptions\OptionNotFoundException("-$c", $commandSpecs->name);
            }

            if (!$optionSpecs->isRepeatable && array_key_exists($optionSpecs->name, $this->options)) {

                throw new Exceptions\OptionRepetitionDeniedException($commandSpecs->name, $optionSpecs->name);
            }

            if ($optionSpecs->isFlag) {

                $this->options[] = new Option(name: $optionSpecs->name);
                continue;
            }

            if (empty($optionValue = substr($token, 2 + $i))) {

                $this->incompleteOption      = $optionSpecs;
                $this->incompleteOptionToken = $optionSpecs->getToken(token: $c, type: Specs\OptionTokenType::Standard);
            } else {

                $this->addOption($optionSpecs, $optionValue);
                return;
            }
        }
    }

    /**
     * Parses an extended option token.
     * 
     * @internal
     * @since 1.0.0
     * @version 1.0.0
     * 
     * @param string        $token        The token to parse.
     * @param Specs\Command $commandSpecs The command specifications.
     * 
     * @return void
     * 
     * @throws Exceptions\OptionNotFoundException If the option is not found in the command specs.
     * @throws Exceptions\MissingOptionArgumentException If the option argument is missing.
     * @throws Exceptions\OptionRepetitionDeniedException If the option is repeated but not allowed.
     */
    private function parseExtendedOption(string $token, Specs\Command $commandSpecs): void {

        $optionTokens = explode('=', substr($token, 2), 2);
        $optionToken  = $optionTokens[0];
        $optionValue  = $optionTokens[1] ?? null;

        if (is_null($optionSpecs = $commandSpecs->getOption(token: $optionToken, tokenType: Specs\OptionTokenType::Extended))) {

            throw new Exceptions\OptionNotFoundException("--$optionToken", $commandSpecs->name);
        }

        if (!$optionSpecs->isRepeatable && array_key_exists($optionSpecs->name, $this->options)) {

            throw new Exceptions\OptionRepetitionDeniedException($commandSpecs->name, $optionSpecs->name);
        }
        
        if ($optionSpecs->isFlag) {

            $this->options[] = new Option(name: $optionSpecs->name);
            return;
        }

        if (is_null($optionValue)) {

            throw new Exceptions\MissingOptionArgumentException($commandSpecs->name, "--$optionToken");
        }

        $this->addOption($optionSpecs, $optionValue);
    }

    /**
     * Adds an option to the parsed options.
     * 
     * @internal
     * @since 1.0.0
     * @version 1.0.0
     * 
     * @param Specs\Option $optionSpecs The specifications of the option to add.
     * @param string       $optionValue The value of the option.
     * 
     * @return void
     */
    private function addOption(Specs\Option $optionSpecs, string $optionValue): void {
        
        if (is_null($existingOption = $this->options[ $optionSpecs->name ] ?? null)) {

            $this->options[ $optionSpecs->name ] = new Option(name: $optionSpecs->name, values: [ $optionValue ]);
        } else {

            $this->options[ $optionSpecs->name ] = new Option(name: $optionSpecs->name, values: [ ...$existingOption->values, $optionValue ]);
        }
    }

    /**
     * Parses an operand token.
     * 
     * @internal
     * @since 1.0.0
     * @version 1.1.0
     * 
     * @param string        $token        The token to parse.
     * @param Specs\Command $commandSpecs The command specifications.
     * 
     * @return void
     */
    private function parseOperand(string $token, Specs\Command $commandSpecs): void {

        $operandIndex = count($this->operands);
        $operandSpecs = $commandSpecs->getOperand(index: $operandIndex);
        $operandValue = $token;

        if (($operandSpecs?->isVariadic ?? false)) {
            
            $operandValue = [ $token ];
        }

        if ($operandIndex > 0 && ($commandSpecs->getOperand(index: $operandIndex - 1)?->isVariadic ?? false)) {

            $operand = array_pop($this->operands);
            $operandValue = [ ...$operand->value, $token ];
        }

        $this->operands[] = new Operand(
            
            value: $operandValue,
            index: $operandIndex,
            name:  $operandSpecs?->name
        );
        
        $this->canAcceptSubCommands = false;
    }

    /**
     * Validates the command specifications for duplicate option tokens.
     * 
     * @internal
     * @since 1.1.0
     * @version 1.0.0
     * 
     * @param Specs\Command $commandSpecs
     * @throws Exceptions\OptionTokenAlreadyDefinedException
     * @return void
     */
    private function validateCommandSpecs(Specs\Command $commandSpecs): void {

        foreach ($commandSpecs->getOptionsNames() as $optionName) {

            $optionSpecs = $commandSpecs->getOption($optionName);

            foreach ($optionSpecs->getTokens() as $optionToken) {

                if (!is_null($this->getOption($commandSpecs, $optionToken, $optionSpecs))) {

                    throw new Exceptions\OptionTokenAlreadyDefinedException(
                        token: $optionToken->type->value . $optionToken->token,
                        option: $optionSpecs->name,
                        commandName: $commandSpecs->name
                    );
                }
            }
        }
    }

    /**
     * Retrieves an option from the command specifications based on the token.
     * 
     * @internal
     * @since 1.1.0
     * @version 1.0.0
     * 
     * @param Specs\Command $commandSpecs
     * @param Specs\OptionToken $token
     * @param Specs\Option $ignore
     * @return Specs\Option|null
     */
    private function getOption(Specs\Command $commandSpecs, Specs\OptionToken $token, Specs\Option $ignore): ?Specs\Option {

        foreach ($commandSpecs->getOptionsNames() as $optionName) {
            
            $optionSpecs = $commandSpecs->getOption($optionName);

            if ($optionSpecs->name == $ignore->name) {

                continue;
            }

            if (!is_null($optionSpecs->getToken(token: $token->token, type: $token->type))) {

                return $optionSpecs;
            }
        }

        return null;
    }

    /**
     * Validates the command against its specifications.
     * 
     * @internal
     * @since 1.1.0
     * @version 1.0.0
     * 
     * @param Command $command
     * @param Specs\Command $commandSpecs
     * @throws Exceptions\MissingRequiredOptionException
     * @throws Exceptions\MissingRequiredOperandException
     * @return void
     */
    private function validateCommand(Command $command, Specs\Command $commandSpecs): void {

        foreach ($commandSpecs->getOptionsNames() as $optionName) {
            
            $optionSpecs = $commandSpecs->getOption($optionName);

            if ($optionSpecs->isRequired) {

                $exists = count(array_filter(
                    $command->options,
                    fn (Option $option) => $option->name == $optionSpecs->name
                ));

                if (!$exists) {

                    throw new Exceptions\MissingRequiredOptionException(
                        command: $commandSpecs->name,
                        optionName: $optionSpecs->name
                    );
                }
            }
        }

        foreach ($commandSpecs->getOperands() as $operand) {

            if ($operand->isRequired) {

                if (is_null($command->operands[$operand->index] ?? null)) {

                    throw new Exceptions\MissingRequiredOperandException(
                        command: $commandSpecs->name,
                        operand: $operand->name ?? $operand->index,
                    );
                }
            }
        }
    }
}
