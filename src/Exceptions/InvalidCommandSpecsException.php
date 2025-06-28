<?php

namespace CommandParser\Exceptions;

/**
 * Invalid Command Specifications Exception.
 *
 * This exception is thrown by the parser if the name of the specified command doesn't match the first token of the command line.
 *
 * @api
 * @final
 * @since 0.1.0
 * @version 1.0.0
 * @package command-parser
 * @author Ali M. Kamel <ali.kamel.dev@gmail.com>
 */
final class InvalidCommandSpecsException extends CommandLineException {

    /**
     * Creates a new Invalid Command Specifications Exception.
     *
     * @api
     * @final
     * @override
     * @since 1.0.0
     * @version 1.0.0
     *
     * @param string|null $description A description of the error, if any.
     */
    public final function __construct(public readonly ?string $description = null) {

        parent::__construct(
            'Invalid command specifications' . (is_null($description) ? '' : ": $description")
        );
    }
}
