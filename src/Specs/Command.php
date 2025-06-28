<?php

namespace CommandParser\Specs;

use CommandParser\Exceptions\{ CommandAlreadyDefinedException, CommandOperandAlreadyDefinedException, CommandOptiondAlreadyDefinedException };

/**
 * Command Specification
 * 
 * @api
 * @final
 * @since 0.1.0
 * @version 1.0.0
 * @package command-parser
 * @author Ali M. Kamel <ali.kamel.dev@gmail.com>
 */
final class Command {

    /**
     * The sub-commands of this command.
     * 
     * @internal
     * @since 1.0.0
     * @var array<string, Command> $subCommands
     */
    private array $subCommands;

    /**
     * The options' specifications of this command.
     * 
     * @internal
     * @since 1.0.0
     * @var array<string, Option> $options
     */
    private array $options;

    /**
     * The operands' specifications of this command.
     * 
     * @internal
     * @since 1.0.0
     * @var Operand[] $operands
     */
    private array $operands;

    /**
     * Creates a new command specification.
     * 
     * @api
     * @final
     * @since 1.0.0
     * @version 1.0.0
     * 
     * @param string $name
     * @param mixed  $description
     * @param array  $options
     * @param array  $operands
     * @param array  $subCommands
     */
    public final function __construct (

        public readonly string  $name,
        public readonly ?string $description = null,
                        array   $options     = [],
                        array   $operands    = [],
                        array   $subCommands = [],
    ) {

        $this->options     = [];
        $this->operands    = [];
        $this->subCommands = [];

        array_walk($options, $this->addOption(...));
        array_walk($operands, $this->addOperand(...));
        array_walk($subCommands, $this->addSubCommand(...));
    }

    /**
     * Adds an option to this command.
     * 
     * @api
     * @final
     * @since 1.0.0
     * @version 1.0.0
     * 
     * @param Option $option
     * @return Command
     * 
     * @throws CommandOptiondAlreadyDefinedException
     */
    public final function addOption(Option $option): static {

        if (isset($this->options[ $option->name ])) {

            throw new CommandOptiondAlreadyDefinedException($option->name, $this->name);
        }

        $this->options[ $option->name ] = $option;

        return $this;
    }

    /**
     * Adds an operand to this command.
     * 
     * @api
     * @final
     * @since 1.0.0
     * @version 1.0.0
     * 
     * @param Operand $operand
     * @return Command
     * 
     * @throws CommandOperandAlreadyDefinedException
     */
    public final function addOperand(Operand $operand): static {

        if (!is_null($this->getOperand(index: $operand->index))) {

            throw new CommandOperandAlreadyDefinedException($operand->index, $this->name);
        }

        if (!is_null($this->getOperand(name: $operand->name))) {

            throw new CommandOperandAlreadyDefinedException($operand->name, $this->name);
        }

        $this->operands[] = $operand;

        return $this;
    }

    /**
     * Adds a sub-command to this command.
     * 
     * @api
     * @final
     * @since 1.0.0
     * @version 1.0.0
     * 
     * @param Command $subCommand
     * @return Command
     * 
     * @throws CommandAlreadyDefinedException
     */
    public final function addSubCommand(Command $subCommand): static {

        if (isset($this->subCommands[ $subCommand->name ])) {

            throw new CommandAlreadyDefinedException($subCommand->name);
        }

        $this->subCommands[ $subCommand->name ] = $subCommand;

        return $this;
    }

    /**
     * Gets an operand by its index or name.
     * 
     * @api
     * @final
     * @since 1.0.0
     * @version 1.0.0
     * 
     * @param int|null    $index
     * @param string|null $name
     * @return Operand|null
     */
    public final function getOperand(?int $index = null, ?string $name = null): ?Operand {

        if (is_null($index ?? $name)) {

            return null;
        }

        foreach ($this->operands as $operand) {

            if (($index !== null && $operand->index === $index) ||
                ($name !== null && $operand->name === $name)) {

                return $operand;
            }
        }

        return null;
    }

    /**
     * Gets an option by its name or token.
     * 
     * @api
     * @final
     * @since 1.0.0
     * @version 1.0.0
     * 
     * @param string|null $name
     * @param string|null $token
     * @param OptionTokenType|null $tokenType
     * @return Option|null
     */
    public final function getOption(?string $name = null, ?string $token = null, ?OptionTokenType $tokenType = null): ?Option {

        if (is_null($name ?? $token)) {

            return null;
        }

        foreach ($this->options as $option) {

            if (($name !== null && $option->name === $name) ||
                ($token !== null && $option->hasToken($token, $tokenType))) {

                return $option;
            }
        }

        return null;
    }

    /**
     * Gets a sub-command by its name.
     * 
     * @api
     * @final
     * @since 1.0.0
     * @version 1.0.0
     * 
     * @param string $name
     * @return Command|null
     */
    public final function getSubCommand(string $name): ?Command {

        foreach ($this->subCommands as $subCommand) {

            if ($subCommand->name === $name) {

                return $subCommand;
            }
        }

        return null;
    }

    /**
     * Checks if this command has a sub-command with the given name.
     * 
     * @api
     * @final
     * @since 1.0.0
     * @version 1.0.0
     * 
     * @param string $name
     * @return bool
     */
    public final function hasSubCommand(string $name): bool {

        return array_key_exists($name, $this->subCommands);
    }
}
