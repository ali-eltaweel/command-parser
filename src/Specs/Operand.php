<?php

namespace CommandParser\Specs;

/**
 * Operand Specification
 *
 * @api
 * @final
 * @since 0.1.0
 * @version 1.0.0
 * @package command-parser
 * @author Ali M. Kamel <ali.kamel.dev@gmail.com>
 */
final class Operand {

    /**
     * Creates a new operand specification.
     *
     * @api
     * @final
     * @since 1.0.0
     * @version 1.0.0
     *
     * @param int|null    $index        The index of the operand.
     * @param string|null $name         The name of the operand.
     * @param string|null $description  The description of the operand.
     */
    public final function __construct(

        public readonly int     $index,
        public readonly ?string $name        = null,
        public readonly ?string $description = null
    ) {}
}
