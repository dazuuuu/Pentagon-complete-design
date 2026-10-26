<?php

/**
 * Small view helpers shared by the WordPress-style admin screens
 * (list table → Add New / Edit screen with a Publish sidebar).
 */

use App\Helpers\Session;

/** Admin-relative URL for a stored media path (admin pages live one level below the web root). */
function pq_admin_media(?string $path): string
{
    $path = trim((string) $path);
    if ($path === '' || preg_match('#^(?:https?:)?//#i', $path)) {
        return $path;
    }
    $path = preg_replace('#^(?:\./|\.\./)+#', '', str_replace('\\', '/', $path)) ?? $path;
    $path = preg_replace('#^/?(?:public_html|public)/#', '', $path) ?? $path;
    return '../' . ltrim($path, '/');
}

function pq_admin_e(?string $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function pq_admin_status_badge(string $status): string
{
    $published = $status === 'active';
    return '<span class="wp-status ' . ($published ? 'is-live' : 'is-draft') . '">'
        . ($published ? 'Published' : 'Hidden') . '</span>';
}

function pq_admin_date(?string $date): string
{
    $ts = $date ? strtotime($date) : false;
    return $ts ? date('Y/m/d', $ts) : '—';
}

function pq_admin_csrf(): string
{
    return '<input type="hidden" name="csrf" value="' . pq_admin_e(Session::csrfToken()) . '">';
}

/**
 * Drag & drop image picker with instant previews. $current is an existing
 * image path to show until a new file is chosen.
 */
function pq_admin_dropzone(string $name, bool $multiple = false, bool $required = false, ?string $current = null, string $hint = ''): string
{
    $field = $multiple ? $name . '[]' : $name;
    $label = $multiple ? 'Drop pictures here or <u>browse</u>' : 'Drop an image here or <u>browse</u>';
    $hint = $hint !== '' ? $hint : 'JPG, PNG, WEBP or GIF. Large photos are resized automatically.';
    $preview = '';
    if ($current) {
        $preview = '<img src="' . pq_admin_e(pq_admin_media($current)) . '" alt="">';
    }

    return '<label class="wp-dropzone' . ($current ? ' has-files' : '') . '" data-dropzone>'
        . '<input type="file" name="' . pq_admin_e($field) . '" accept="image/*"'
        . ($multiple ? ' multiple' : '') . ($required ? ' required' : '') . '>'
        . '<span class="wp-dropzone-empty"><i class="bi bi-cloud-arrow-up"></i><strong>' . $label . '</strong>'
        . '<small>' . pq_admin_e($hint) . '</small></span>'
        . '<span class="wp-dropzone-previews">' . $preview . '</span>'
        . '</label>';
}

/**
 * Shared hidden form used by row-level "Delete" links so they don't need a
 * nested <form> inside the bulk-actions form.
 */
function pq_admin_row_action_form(string $id = 'wp-row-action'): string
{
    return '<form method="POST" id="' . pq_admin_e($id) . '" hidden>' . pq_admin_csrf() . '</form>';
}

/**
 * Spreadsheet-style record list with a slide-in editor.
 *
 * Renders a numbered, read-only grid (one row per record; the advanced search
 * in the footer attaches to it automatically) plus one off-canvas form that
 * opens pre-filled when a row is clicked, or empty for "Add New". The form
 * posts the full record with the page's existing create/update/delete
 * actions, so page handlers stay unchanged.
 *
 * $config keys:
 *   id        unique DOM id for this sheet
 *   title     heading; singular: "Add <singular>" / "Edit <singular>"
 *   rows      records (arrays with an 'id')
 *   columns   [['label' => …, 'value' => fn($row) => string, 'html' => bool, 'class' => …,
 *               'filter' => bool (dropdown filter in the search bar; Status always gets one)], …]
 *   fields    [['name' => …, 'label' => …, 'type' => text|textarea|number|price|select|checkbox|
 *               datetime|url|status|hidden|image|images|video, 'required' => bool, 'col' => 12|6|4,
 *               'options' => [value => label], 'default' => …, 'placeholder' => …, 'help' => …,
 *               'preview' => fn($row) => ?string (image types)], …]
 *   thumb     optional fn($row) => ?string image path shown in its own column
 *   gallery   optional fn($row) => [['id' => …, 'path' => …], …] (removable via action delete_image)
 *   actions   optional ['create' => …, 'update' => …, 'delete' => …] action names
 *   intro     optional muted text under the heading
 *   empty     message when there are no rows
 */
function pq_admin_sheet(array $config): string
{
    $id = $config['id'];
    $rows = $config['rows'] ?? [];
    $fields = $config['fields'] ?? [];
    $columns = $config['columns'] ?? [];
    $thumb = $config['thumb'] ?? null;
    $gallery = $config['gallery'] ?? null;
    $actions = ($config['actions'] ?? []) + ['create' => 'create', 'update' => 'update', 'delete' => 'delete'];
    $singular = $config['singular'] ?? 'Item';
    $multipart = (bool) array_filter($fields, fn ($f) => in_array($f['type'] ?? 'text', ['image', 'images', 'video'], true));

    ob_start();
    ?>
<div class="sheet-head">
  <div>
    <h2><?php echo pq_admin_e($config['title']); ?> <span class="sheet-count"><?php echo count($rows); ?></span></h2>
    <?php if (!empty($config['intro'])): ?><p><?php echo pq_admin_e($config['intro']); ?></p><?php endif; ?>
  </div>
  <button type="button" class="btn btn-primary" data-sheet-new="<?php echo pq_admin_e($id); ?>"><i class="bi bi-plus-lg"></i> Add <?php echo pq_admin_e($singular); ?></button>
</div>
<div class="content-card p-3 mb-4">
  <?php if ($rows === []): ?>
  <div class="wp-empty"><i class="bi bi-table"></i><?php echo pq_admin_e($config['empty'] ?? 'Nothing here yet.'); ?>
    <div class="mt-2"><button type="button" class="btn btn-sm btn-outline-primary" data-sheet-new="<?php echo pq_admin_e($id); ?>">Add the first one</button></div>
  </div>
  <?php else: ?>
  <div class="table-responsive sheet-wrap">
    <table class="table sheet-table" data-sheet="<?php echo pq_admin_e($id); ?>">
      <thead><tr>
        <th class="sheet-num">#</th>
        <?php if ($thumb): ?><th class="sheet-thumb-col"></th><?php endif; ?>
        <?php foreach ($columns as $col): ?><th<?php echo !empty($col['filter']) || $col['label'] === 'Status' ? ' data-filter' : ''; ?>><?php echo pq_admin_e($col['label']); ?></th><?php endforeach; ?>
        <th class="sheet-actions-col"></th>
      </tr></thead>
      <tbody>
        <?php foreach ($rows as $n => $row): ?>
        <?php
          $record = ['id' => (int) $row['id']];
          foreach ($fields as $f) {
              $type = $f['type'] ?? 'text';
              if (in_array($type, ['image', 'images', 'video'], true)) {
                  if (isset($f['preview'])) {
                      $p = ($f['preview'])($row);
                      $record['_preview_' . $f['name']] = $p ? pq_admin_media($p) : '';
                  }
                  continue;
              }
              $record[$f['name']] = $row[$f['name']] ?? '';
          }
          if ($gallery) {
              $record['_gallery'] = array_map(fn ($img) => ['id' => (int) $img['id'], 'src' => pq_admin_media($img['path'])], ($gallery)($row));
          }
        ?>
        <tr data-record="<?php echo pq_admin_e(json_encode($record)); ?>" tabindex="0">
          <td class="sheet-num"><?php echo $n + 1; ?></td>
          <?php if ($thumb): ?>
          <td class="sheet-thumb-col">
            <?php $src = ($thumb)($row); ?>
            <?php if ($src): ?><img class="sheet-thumb" src="<?php echo pq_admin_e(pq_admin_media($src)); ?>" alt="" loading="lazy" onerror="this.replaceWith(document.createElement('span'))"><?php else: ?><span class="sheet-thumb sheet-thumb-empty"><i class="bi bi-image"></i></span><?php endif; ?>
          </td>
          <?php endif; ?>
          <?php foreach ($columns as $col): ?>
          <?php $value = ($col['value'])($row); ?>
          <td class="<?php echo pq_admin_e($col['class'] ?? ''); ?>"><?php echo !empty($col['html']) ? $value : pq_admin_e((string) $value); ?></td>
          <?php endforeach; ?>
          <td class="sheet-actions-col"><button type="button" class="btn btn-sm btn-light sheet-edit" title="Edit"><i class="bi bi-pencil-square"></i> Edit</button></td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
  <?php endif; ?>
</div>

<div class="offcanvas offcanvas-end sheet-editor" tabindex="-1" id="sheet-<?php echo pq_admin_e($id); ?>" data-singular="<?php echo pq_admin_e($singular); ?>">
  <form method="POST"<?php echo $multipart ? ' enctype="multipart/form-data"' : ''; ?> class="d-flex flex-column h-100" data-sheet-form
        data-create="<?php echo pq_admin_e($actions['create']); ?>" data-update="<?php echo pq_admin_e($actions['update']); ?>">
    <?php echo pq_admin_csrf(); ?>
    <input type="hidden" name="action" value="<?php echo pq_admin_e($actions['create']); ?>">
    <input type="hidden" name="id" value="">
    <div class="offcanvas-header">
      <h5 class="offcanvas-title" data-sheet-title>Add <?php echo pq_admin_e($singular); ?></h5>
      <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body">
      <div class="row g-3">
        <?php foreach ($fields as $f): ?>
        <?php
          $type = $f['type'] ?? 'text';
          $name = $f['name'];
          $label = $f['label'] ?? '';
          $req = !empty($f['required']);
          $col = (int) ($f['col'] ?? 12);
          $ph = pq_admin_e($f['placeholder'] ?? '');
          $default = pq_admin_e((string) ($f['default'] ?? ''));
          if ($type === 'hidden') {
              echo '<input type="hidden" name="' . pq_admin_e($name) . '" data-default="' . $default . '">';
              continue;
          }
        ?>
        <div class="col-md-<?php echo $col; ?>">
          <?php if ($type === 'checkbox'): ?>
          <div class="form-check form-switch sheet-switch">
            <input class="form-check-input" type="checkbox" role="switch" name="<?php echo pq_admin_e($name); ?>" value="1" id="f-<?php echo pq_admin_e($id . '-' . $name); ?>" data-default="<?php echo $default; ?>">
            <label class="form-check-label" for="f-<?php echo pq_admin_e($id . '-' . $name); ?>"><?php echo pq_admin_e($label); ?></label>
          </div>
          <?php else: ?>
          <label class="form-label"><?php echo pq_admin_e($label); ?><?php echo $req ? ' <span class="text-danger">*</span>' : ''; ?></label>
          <?php if ($type === 'textarea'): ?>
          <textarea class="form-control" name="<?php echo pq_admin_e($name); ?>" rows="<?php echo (int) ($f['rows'] ?? 4); ?>" placeholder="<?php echo $ph; ?>" data-default="<?php echo $default; ?>"<?php echo $req ? ' required' : ''; ?>></textarea>
          <?php elseif ($type === 'select' || $type === 'status'): ?>
          <?php $options = $type === 'status' ? ['active' => 'Published', 'inactive' => 'Hidden'] : ($f['options'] ?? []); ?>
          <select class="form-select" name="<?php echo pq_admin_e($name); ?>" data-default="<?php echo $type === 'status' ? 'active' : $default; ?>"<?php echo $req ? ' required' : ''; ?>>
            <?php foreach ($options as $value => $text): ?><option value="<?php echo pq_admin_e((string) $value); ?>"><?php echo pq_admin_e($text); ?></option><?php endforeach; ?>
          </select>
          <?php elseif ($type === 'image' || $type === 'images'): ?>
          <div data-sheet-preview="<?php echo pq_admin_e($name); ?>"><?php echo pq_admin_dropzone($name, $type === 'images', $req); ?></div>
          <?php elseif ($type === 'video'): ?>
          <div class="sheet-video-current small text-muted mb-1" data-sheet-video="<?php echo pq_admin_e($name); ?>"></div>
          <input class="form-control" type="file" name="<?php echo pq_admin_e($name); ?>" accept="video/mp4,video/webm,video/quicktime">
          <?php else: ?>
          <?php $inputType = ['number' => 'number', 'price' => 'number', 'datetime' => 'datetime-local', 'url' => 'url'][$type] ?? 'text'; ?>
          <input class="form-control" type="<?php echo $inputType; ?>" name="<?php echo pq_admin_e($name); ?>" placeholder="<?php echo $ph; ?>" data-default="<?php echo $default; ?>"<?php echo $type === 'price' ? ' step="0.01" min="0"' : ''; ?><?php echo $req ? ' required' : ''; ?>>
          <?php endif; ?>
          <?php endif; ?>
          <?php if (!empty($f['help'])): ?><div class="form-text"><?php echo pq_admin_e($f['help']); ?></div><?php endif; ?>
        </div>
        <?php endforeach; ?>

        <?php if ($gallery): ?>
        <div class="col-12" data-sheet-gallery-wrap>
          <label class="form-label">Current pictures</label>
          <div class="wp-media-grid" data-sheet-gallery></div>
          <div class="small text-muted" data-sheet-gallery-empty>No extra pictures yet.</div>
        </div>
        <?php endif; ?>
      </div>
    </div>
    <div class="sheet-editor-foot">
      <button type="submit" class="wp-delete-link" form="sheet-<?php echo pq_admin_e($id); ?>-delete" data-sheet-delete data-confirm="Delete this <?php echo pq_admin_e(strtolower($singular)); ?> permanently?">Delete</button>
      <div class="ms-auto d-flex gap-2">
        <button type="button" class="btn btn-light" data-bs-dismiss="offcanvas">Cancel</button>
        <button type="submit" class="btn btn-primary px-4"><i class="bi bi-check2"></i> Save</button>
      </div>
    </div>
  </form>
</div>
<form method="POST" id="sheet-<?php echo pq_admin_e($id); ?>-delete" hidden>
  <?php echo pq_admin_csrf(); ?>
  <input type="hidden" name="action" value="<?php echo pq_admin_e($actions['delete']); ?>">
  <input type="hidden" name="id" value="">
</form>
<?php if ($gallery): ?>
<form method="POST" id="sheet-<?php echo pq_admin_e($id); ?>-image" hidden>
  <?php echo pq_admin_csrf(); ?>
  <input type="hidden" name="action" value="delete_image">
  <input type="hidden" name="id" value="">
</form>
<?php endif; ?>
    <?php
    return (string) ob_get_clean();
}

/** Flash alerts in the standard admin style. */
function pq_admin_alerts(?string $message, ?string $error): string
{
    $html = '';
    if ($message) {
        $html .= '<div class="alert alert-success alert-dismissible fade show">' . pq_admin_e($message)
            . '<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>';
    }
    if ($error) {
        $html .= '<div class="alert alert-danger">' . pq_admin_e($error) . '</div>';
    }
    return $html;
}
