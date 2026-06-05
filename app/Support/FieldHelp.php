<?php

namespace App\Support;

class FieldHelp
{
    public static function moduleName(): string
    {
        return 'Topic name shown in admin lists and dropdowns. e.g. lecture 5, Chart patterns, Risk management.';
    }

    public static function moduleTradingStyle(): string
    {
        return 'Optional. Chart period or trading style for this topic (e.g. Intraday: 1 min, 5 min; Swing: 15 min–2 h; Long term: 1–2 years). Not lecture or video length. Leave blank if you do not use trading-style topics.';
    }

    public static function moduleDescription(): string
    {
        return 'Optional summary of what this module covers. Shown under the module name in admin lists.';
    }

    public static function batchName(): string
    {
        return 'Student cohort name. e.g. Feb batch, November 2026 cohort.';
    }

    public static function lectureBatch(): string
    {
        return 'Which student batch this lecture belongs to.';
    }

    public static function lectureModule(): string
    {
        return 'Topic folder for this lecture. e.g. lecture 5, Chart patterns.';
    }

    public static function lectureTitle(): string
    {
        return 'Lesson name shown to students. e.g. Chart analysis combination, Risk management basics.';
    }

    public static function lectureNotes(): string
    {
        return 'Admin notes for this lecture. Visible to students on the learning page for this batch and module.';
    }

    public static function videoLecture(): string
    {
        return 'Parent lecture this video is attached to.';
    }

    public static function videoLabel(): string
    {
        return 'Short name in admin lists. e.g. Part 1, Main session, Revision.';
    }

    public static function videoYoutubeUrl(): string
    {
        return 'Full YouTube watch link. The YouTube title is fetched automatically when possible.';
    }

    public static function videoType(): string
    {
        return 'Optional category label. e.g. Main, Supplementary, Q&A.';
    }

    public static function documentLecture(): string
    {
        return 'Parent lecture this file is attached to.';
    }

    public static function documentTitle(): string
    {
        return 'Display name for the file. e.g. Session slides, Homework PDF.';
    }

    public static function documentFile(): string
    {
        return 'Upload PDF, PPT, or PPTX. Max size limits apply per server settings.';
    }

    public static function enrollmentBatch(): string
    {
        return 'Batch the student will belong to.';
    }

    public static function enrollmentStudent(): string
    {
        return 'Student account to enroll. Only user-role accounts can be enrolled.';
    }

    public static function sortOrder(string $context = 'items'): string
    {
        return "Controls the order {$context} appear in lists and dropdowns. Lower numbers show first (0 before 1).";
    }

    public static function active(string $context = 'item'): string
    {
        return "Inactive {$context}s are hidden from student views and selection dropdowns.";
    }
}
