<?php

namespace CommandParser\Exceptions;

/**
 * Command Already Defined Exception.
 * 
 * @api
 * @final
 * @since 0.1.0
 * @version 1.0.0
 * @package command-parser
 * @author Ali M. Kamel <ali.kamel.dev@gmail.com>
 */
final class CommandAlreadyDefinedException extends CommandLineException {

    /**
     * Creates a new Command Already Defined Exception.
     *
     * @api
     * @final
     * @override
     * @since 1.0.0
     * @version 1.0.0
     *
     * @param string      $commandName       The name of the command that is already defined.
     * @param ?string     $parentCommandName The name of the parent command, if any.
     */
    public final function __construct(public readonly string $commandName, public readonly ?string $parentCommandName = null) {

        if (is_null($parentCommandName)) {

            parent::__construct("The command '$commandName' is already defined");
        } else {

            parent::__construct("The command '$commandName' is already defined in the command '$parentCommandName'");
        }
    }
}
