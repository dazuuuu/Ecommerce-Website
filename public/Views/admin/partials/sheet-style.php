<?php /* Shared Excel-style grid styling for admin sheets (products, offers, record sale). */ ?>
<style>
  .xl-sheet { border-collapse: separate; border-spacing: 0; font-size: 13px; }
  .xl-sheet th, .xl-sheet td { border-right: 1px solid #d4dbe6; border-bottom: 1px solid #d4dbe6; white-space: nowrap; }
  .xl-sheet thead th { position: sticky; z-index: 2; }
  .xl-sheet .xl-letters th { top: 0; background: #f1f5f9 !important; color: #64748b; font-weight: 500; font-size: 11px; text-align: center; padding: 3px 8px; }
  .xl-sheet .xl-heads th { top: 23px; background: #eff6ff !important; color: #1e293b; font-weight: 600; padding: 9px 12px; text-align: left; cursor: pointer; user-select: none; }
  .xl-sheet .xl-heads th[data-sort-dir="asc"]::after { content: " ▲"; font-size: 9px; color: #2563eb; }
  .xl-sheet .xl-heads th[data-sort-dir="desc"]::after { content: " ▼"; font-size: 9px; color: #2563eb; }
  .xl-sheet .xl-rownum { position: sticky; left: 0; z-index: 1; background: #f1f5f9; color: #64748b; text-align: center; font-size: 11px; width: 44px; min-width: 44px; }
  .xl-sheet thead .xl-rownum { z-index: 3; }
  .xl-sheet tbody td { padding: 6px 12px; background: #fff; color: #0f172a; height: 44px; }
  .xl-sheet tbody tr:hover td { background: #f8fbff; }
  .xl-sheet tbody tr:hover .xl-rownum { background: #dbeafe; color: #1d4ed8; }
  .xl-sheet td.xl-num { text-align: right; font-variant-numeric: tabular-nums; }
  .xl-sheet td.xl-selected, .xl-sheet td:focus-within { outline: 2px solid #2563eb; outline-offset: -2px; }
  .xl-sheet td.xl-input { padding: 0; }
  .xl-sheet td.xl-input input, .xl-sheet td.xl-input select { width: 100%; height: 44px; border: 0 !important; border-radius: 0 !important; background: transparent; padding: 0 12px; font-size: 13px; box-shadow: none !important; }
  .xl-sheet td.xl-input input[type="number"] { text-align: right; font-variant-numeric: tabular-nums; }
  .xl-sheet td.xl-input input:focus { background: #fff; }
  .xl-sheet td.xl-editable { background: #fefce8; }
  .xl-sheet tfoot td { position: sticky; bottom: 0; background: #f8fafc; font-weight: 700; padding: 8px 12px; border-top: 2px solid #94a3b8; border-right: 1px solid #d4dbe6; }
  .xl-sheet tfoot .xl-rownum { background: #f1f5f9; }
  .xl-formula { display: flex; align-items: center; border-top: 1px solid #d4dbe6; font-size: 13px; }
  .xl-formula span { padding: 6px 12px; border-right: 1px solid #d4dbe6; color: #64748b; font-style: italic; font-family: Georgia, serif; }
  .xl-formula output { padding: 6px 12px; color: #0f172a; font-family: ui-monospace, monospace; }
</style>
