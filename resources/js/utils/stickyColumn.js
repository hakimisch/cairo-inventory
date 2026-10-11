/**
 * Pinned action column for the wide data tables.
 *
 * The asset / verification / receiving tables render ~1500-1600px wide, but the
 * content area is only ~1100px wide at a 1440px window (sidebar + padding). The
 * action column (Hapus / Verify / Receive / Edit ...) therefore sat past the
 * right edge of the screen, and the only way to reach it was the horizontal
 * scrollbar at the bottom of the table — below the fold, so the delete button
 * looked like it was missing entirely.
 *
 * Pinning the column to the right edge of the scroll container keeps every
 * action button visible at any window width. `background` must be opaque (the
 * page passes the row colour) or the cells scrolling underneath would show
 * through; the box-shadow replaces the border, because collapsed table borders
 * do not travel with a sticky cell.
 */

export const pinnedHead = (background) => ({
    position : 'sticky',
    right    : 0,
    zIndex   : 3,
    background,
    boxShadow: '-8px 0 8px -8px rgba(0, 0, 0, 0.18)',
});

export const pinnedCell = (background) => ({
    position : 'sticky',
    right    : 0,
    zIndex   : 2,
    background,
    boxShadow: '-8px 0 8px -8px rgba(0, 0, 0, 0.18)',
});
