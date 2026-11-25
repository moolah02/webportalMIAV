<?php

namespace App\Support;

/**
 * The fixed choices a visit form offers, in one place.
 *
 * These lists were copied inline into two controllers and four Blade views,
 * which is exactly how they drift: renaming a choice meant finding six copies,
 * and a missed one silently rejects a value the form itself offered. Anything
 * that offers or validates these choices reads them from here.
 *
 * The mobile app keeps its own copy in `lib/core/constants/app_constants.dart`
 * - it has to work offline - so the two must be compared as sets whenever
 * either changes.
 */
class VisitOptions
{
    /**
     * Corrective action taken, as offered today.
     *
     * After the November 2026 training the technicians asked for the wording
     * to describe what they DID, not what still needs doing: "To collect
     * device" became "Collected", and "Replaced" was added next to the
     * existing "Replacement needed".
     */
    public const CORRECTIVE_ACTIONS = [
        'Resolved',
        'No action needed',
        'Collected',
        'Follow-up needed',
        'Replacement needed',
        'Replaced',
    ];

    /**
     * Wording retired from CORRECTIVE_ACTIONS but still held by visits already
     * recorded. Never offered as a new choice, only tolerated - otherwise
     * opening an older visit and pressing Save fails validation on a value the
     * office never typed.
     */
    public const CORRECTIVE_ACTIONS_RETIRED = [
        'To collect device',
    ];

    /**
     * The approved terminal models.
     *
     * This was free text, which let one device be recorded as "x990", "X990"
     * and "vx990" - three models as far as any report could tell.
     */
    public const TERMINAL_MODELS = [
        'VX520',
        'VX675',
        'X990',
        'Ingenico SIS',
        'Ingenico ZSS',
        'VX',
        // Added 2026-11-25: the sheets being imported hold VL devices, which
        // were not in the six Nonoe first listed.
        'VL',
    ];

    /** Everything a stored corrective action is allowed to be. */
    public static function correctiveActionsAccepted(): array
    {
        return array_merge(self::CORRECTIVE_ACTIONS, self::CORRECTIVE_ACTIONS_RETIRED);
    }

    /**
     * An `in:` rule listing every accepted corrective action.
     *
     * Laravel's `in:` splits on commas, so a value containing one could never
     * be matched. None do today; this guards the next addition rather than
     * failing quietly if one ever does.
     */
    public static function correctiveActionRule(): string
    {
        $accepted = array_filter(
            self::correctiveActionsAccepted(),
            fn ($value) => ! str_contains($value, ','),
        );

        return 'in:'.implode(',', $accepted);
    }

    /**
     * The approved spelling of a model, matched ignoring case and spacing, or
     * null when it is not an approved model.
     *
     * Mirrors AppConstants.canonicalTerminalModel in the app, so the portal
     * and the tablet agree on what "x990" means.
     */
    public static function canonicalTerminalModel(?string $raw): ?string
    {
        if ($raw === null) {
            return null;
        }

        $key = strtolower(preg_replace('/[\s_-]+/', '', $raw) ?? '');
        if ($key === '') {
            return null;
        }

        foreach (self::TERMINAL_MODELS as $model) {
            if (strtolower(preg_replace('/[\s_-]+/', '', $model) ?? '') === $key) {
                return $model;
            }
        }

        return null;
    }

    /**
     * The models to offer, with any unapproved value the record already holds
     * kept on the end rather than dropped the first time someone saves.
     *
     * @return array<string, string>  value => label
     */
    public static function terminalModelChoices(?string $current = null): array
    {
        $choices = [];
        foreach (self::TERMINAL_MODELS as $model) {
            $choices[$model] = $model;
        }

        $current = trim((string) $current);
        if ($current !== '' && self::canonicalTerminalModel($current) === null) {
            $choices[$current] = $current.' (on record — not an approved model)';
        }

        return $choices;
    }
}
