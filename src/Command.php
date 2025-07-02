<?php

namespace CommandParser;

/**
 * Parsed Commandline.
 * 
 * @api
 * @final
 * @since 0.1.0
 * @version 1.1.0
 * @package command-parser
 * @author Ali M. Kamel <ali.kamel.dev@gmail.com>
 */
final class Command {

    /**
     * Creates a new Command.
     *
     * @api
     * @final
     * @since 1.0.0
     * @version 1.0.0
     *
     * @param string $name        The name of the command.
     * @param array  $options     The options appeared on the commandline.
     * @param array  $operands    The operands passed to the command.
     * @param array  $subCommands The sub-commands parsed from the command line.
     */
    public final function __construct(

        public readonly string $name,
        public readonly array $options = [],
        public readonly array $operands = [],
        public readonly array $subCommands = [],
    ) {}

    /**
     * Retrieves the sub-command.
     * 
     * @api
     * @final
     * @since 1.1.0
     * @version 1.0.0
     * 
     * @return Command|null Returns the sub-command if exists, otherwise null.
     */
    public final function getSubCommand(): ?Command {

        return $this->subCommands[0] ?? null;
    }

    /**
     * Retrieves an option by its name.
     * 
     * @api
     * @final
     * @since 1.1.0
     * @version 1.0.0
     * 
     * @param string $name
     * @return Option|null Returns the option if exists, otherwise null.
     */
    public final function getOption(string $name): ?Option {

        foreach ($this->options as $option) {
            
            if ($option->name === $name) {
                
                return $option;
            }
        }

        return null;
    }

    /**
     * Retrieves an operand by its index or name.
     * 
     * @api
     * @final
     * @since 1.1.0
     * @version 1.0.0
     * 
     * @param int|null $index The index of the operand.
     * @param string|null $name The name of the operand.
     * @return Operand|null Returns the operand if exists, otherwise null.
     */
    public final function getOperand(?int $index = null, ?string $name = null): ?Operand {

        if (is_null($index ?? $name)) {

            return null;
        }

        foreach ($this->operands as $operand) {
            
            if ((!is_null($index) && $operand->index === $index) || (!is_null($name) && $operand->name === $name)) {
                
                return $operand;
            }
        }

        return null;
    }
}
