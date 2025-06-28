<?php

namespace CommandParser\Exceptions;

/**
 * Missing Required Operand Exception.
 *
 * @api
 * @final
 * @since 1.0.0
 * @version 1.0.0
 * @package command-parser
 * @author Ali M. Kamel <ali.kamel.dev@gmail.com>
 */
final class MissingRequiredOperandException extends CommandLineException {

    /**
     * Creates a new Missing Required Operand Exception.
     * 
     * @api
     * @final
     * @override
     * @since 1.0.0
     * @version 1.0.0
     * 
     * @param string $command
     * @param string|int $operand
     */
    public final function __construct(public readonly string $command, public readonly string|int $operand) {

        parent::__construct(
            "Missing required operand '{$operand}' for command '{$command}'.",
        );
    }
}
