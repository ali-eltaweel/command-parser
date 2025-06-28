<?php

namespace CommandParser\Exceptions;

/**
 * Commandline Operand Already Defined Exception.
 * 
 * @api
 * @final
 * @since 0.1.0
 * @version 1.0.0
 * @package command-parser
 * @author Ali M. Kamel <ali.kamel.dev@gmail.com>
 */
final class CommandOperandAlreadyDefinedException extends CommandLineException {

    /**
     * Creates a new Command Operand Already Defined Exception.
     *
     * @api
     * @final
     * @since 1.0.0
     * @version 1.0.0
     *
     * @param int|string $operand     The index/name of the operand that is already defined.
     * @param string     $commandName The name of the command that defines the operand.
     */
    public final function __construct(public readonly int|string $operand, public readonly string $commandName) {

        parent::__construct("The operand '$operand' is already defined in the command '$commandName'");
    }
}
