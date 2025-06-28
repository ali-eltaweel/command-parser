<?php

namespace CommandParser\Exceptions;

/**
 * Missing Required Option Exception.
 *
 * @api
 * @final
 * @since 1.0.0
 * @version 1.0.0
 * @package command-parser
 * @author Ali M. Kamel <ali.kamel.dev@gmail.com>
 */
final class MissingRequiredOptionException extends CommandLineException {

    /**
     * Creates a new Missing Required Option Exception.
     * 
     * @api
     * @final
     * @override
     * @since 1.0.0
     * @version 1.0.0
     * 
     * @param string $command
     * @param string $optionName
     */
    public final function __construct(public readonly string $command, public readonly string $optionName) {

        parent::__construct(
            "Missing required option '{$optionName}' for command '{$command}'.",
        );
    }
}
