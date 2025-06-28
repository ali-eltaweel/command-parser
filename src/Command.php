<?php

namespace CommandParser;

/**
 * Parsed Commandline.
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
}
