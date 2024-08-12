<?php

declare(strict_types=1);

use PhpCsFixer\Fixer\CastNotation\CastSpacesFixer;
use PhpCsFixer\Fixer\ClassNotation\ClassAttributesSeparationFixer;
use PhpCsFixer\Fixer\ControlStructure\TrailingCommaInMultilineFixer;
use PhpCsFixer\Fixer\Operator\ConcatSpaceFixer;
use PhpCsFixer\Fixer\Operator\NotOperatorWithSuccessorSpaceFixer;
use PhpCsFixer\Fixer\Phpdoc\NoSuperfluousPhpdocTagsFixer;
use PhpCsFixer\Fixer\Phpdoc\PhpdocAlignFixer;
use PhpCsFixer\Fixer\Phpdoc\PhpdocToCommentFixer;
use PhpCsFixer\Fixer\StringNotation\SingleQuoteFixer;
use Symplify\EasyCodingStandard\Config\ECSConfig;
use Symplify\EasyCodingStandard\ValueObject\Option;
use Symplify\EasyCodingStandard\ValueObject\Set\SetList;

return static function (ECSConfig $ecsConfig): void {
    $ecsConfig->paths(
        [
            __DIR__ . '/src',
            __DIR__ . '/tests',
            __FILE__,
        ],
    );

    $ecsConfig->skip([
        NotOperatorWithSuccessorSpaceFixer::class,
        PhpdocAlignFixer::class,
        PhpdocToCommentFixer::class => 'src/Entity',
        NoSuperfluousPhpdocTagsFixer::class,
    ]);

    $ecsConfig->sets(
        [
            SetList::PSR_12,
            SetList::CLEAN_CODE,
            SetList::ARRAY,
            SetList::STRICT,
            SetList::SPACES,
            SetList::SYMPLIFY,
            SetList::COMMON,
            SetList::COMMENTS,
        ],
    );

    $ecsConfig->rule(SingleQuoteFixer::class);
    $ecsConfig->ruleWithConfiguration(TrailingCommaInMultilineFixer::class, [
        'elements' => [
            'arrays',
            'parameters',
        ],
    ]);
    $ecsConfig->ruleWithConfiguration(
        ClassAttributesSeparationFixer::class,
        [
            'elements' => [
                'const' => 'one',
                'property' => 'one',
                'method' => 'one',
            ],
        ],
    );

    $ecsConfig->ruleWithConfiguration(
        ConcatSpaceFixer::class,
        [
            'spacing' => 'one',
        ],
    );

    $ecsConfig->ruleWithConfiguration(
        CastSpacesFixer::class,
        [
            'space' => 'none',
        ],
    );
};
