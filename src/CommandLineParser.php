<?php

namespace CommandParser;

/**
 * Commandline Parser.
 * 
 * @api
 * @final
 * @since 0.1.0
 * @version 1.0.0
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
     * @version 1.0.0
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

                try {

                    $this->subCommands[] = (new static)->parse(
                        array_slice($commandLine, $i),
                        $commandSpecs->getSubCommand($token)
                    );
                } catch (Exceptions\OptionNotFoundException $e) {

                    throw new Exceptions\OptionNotFoundException($e->option, "{$commandSpecs->name} {$e->command}");

                } catch (Exceptions\MissingOptionArgumentException $e) {

                    throw new Exceptions\MissingOptionArgumentException("{$commandSpecs->name} {$e->command}", $e->optionToken);
                    
                } catch (Exceptions\OptionRepetitionDeniedException $e) {

                    throw new Exceptions\OptionRepetitionDeniedException("{$commandSpecs->name} {$e->command}", $e->optionName);
                }

                break;
            }

            $this->parseOperand($token, $commandSpecs);
        }

        if (!is_null($this->incompleteOption)) {

            throw new Exceptions\MissingOptionArgumentException($commandSpecs->name, "-{$this->incompleteOptionToken->token}");
        }

        return new Command(

            name: $commandSpecs->name,
            options: array_values($this->options),
            operands: $this->operands,
            subCommands: $this->subCommands
        );
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
     * @version 1.0.0
     * 
     * @param string        $token        The token to parse.
     * @param Specs\Command $commandSpecs The command specifications.
     * 
     * @return void
     */
    private function parseOperand(string $token, Specs\Command $commandSpecs): void {

        $operandIndex = count($this->operands);

        $this->operands[] = new Operand(
            
            value: $token,
            index: $operandIndex,
            name:  $commandSpecs->getOperand(index: $operandIndex)?->name
        );
        
        $this->canAcceptSubCommands = false;
    }
}
