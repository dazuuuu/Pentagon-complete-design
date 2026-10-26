<?php

require_once __DIR__ . '/../includes/bootstrap.php';

use App\Core\Auth;
use App\Helpers\Session;
use App\Helpers\Upload;
use App\Services\ClientService;
use App\Services\TestimonialService;
use App\Services\ClientAppointmentService;
use App\Services\ClientTravelService;
use App\Services\ClientFollowUpService;

Auth::requireAuth();
Session::start();

$service = new ClientService();
$testimonialService = new TestimonialService();
$appointmentService = new ClientAppointmentService();
$travelService = new ClientTravelService();
$followUpService = new ClientFollowUpService();
$message = Session::flash('message');
$error = Session::flash('error');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && Session::verifyCsrf($_POST['csrf'] ?? '')) {
  $action = $_POST['action'] ?? '';
  $id = (int) ($_POST['id'] ?? 0);

  try {
    if ($action === 'register') {
      $fullName = trim($_POST['full_name'] ?? '');
      if ($fullName === '') {
        throw new \RuntimeException('Full name is required.');
      }
      $profilePicture = Upload::store($_FILES['profile_picture'] ?? [], 'clients');
      $service->register([
        'full_name' => $fullName,
        'email' => trim($_POST['email'] ?? '') ?: null,
        'phone' => trim($_POST['phone'] ?? '') ?: null,
        'country_of_origin' => trim($_POST['country_of_origin'] ?? '') ?: null,
        'purpose_of_visit' => trim($_POST['purpose_of_visit'] ?? '') ?: null,
        'profile_picture' => $profilePicture,
        'status' => $_POST['status'] ?? 'scheduled',
        'scheduled_date' => trim($_POST['scheduled_date'] ?? '') ?: null,
      ]);
      Session::flash('message', 'Client registered — welcome email sent.');
    } elseif ($action === 'update_profile') {
      $client = $service->find($id);
      if ($client) {
        $profilePicture = Upload::store($_FILES['profile_picture'] ?? [], 'clients') ?? $client['profile_picture'];
        $service->update($id, [
          'full_name' => trim($_POST['full_name'] ?? '') ?: $client['full_name'],
          'email' => trim($_POST['email'] ?? '') ?: null,
          'phone' => trim($_POST['phone'] ?? '') ?: null,
          'interest' => $client['interest'],
          'country_of_origin' => trim($_POST['country_of_origin'] ?? '') ?: null,
          'purpose_of_visit' => trim($_POST['purpose_of_visit'] ?? '') ?: null,
          'profile_picture' => $profilePicture,
          'status' => $client['status'],
          'scheduled_date' => $client['scheduled_date'],
        ]);
        Session::flash('message', 'Client profile updated.');
      }
    } elseif ($action === 'update_status') {
      $service->updateStatus($id, $_POST['status'] ?? 'scheduled', trim($_POST['scheduled_date'] ?? '') ?: null);
      Session::flash('message', 'Client status updated — customer notified by email.');
    } elseif ($action === 'delete') {
      $service->delete($id);
      Session::flash('message', 'Client removed.');
    } elseif ($action === 'add_testimonial') {
      $client = $service->find($id);
      if ($client) {
        $testimonialService->create([
          'client_id' => $client['id'],
          'author_name' => $client['full_name'],
          'author_location' => trim($_POST['author_location'] ?? '') ?: ($client['interest'] ?? ''),
          'quote' => trim($_POST['quote'] ?? ''),
          'status' => 'inactive',
        ]);
        Session::flash('message', 'Testimonial submitted — review and publish it below when ready.');
      }
    } elseif ($action === 'toggle_testimonial') {
      $testimonial = $testimonialService->find((int) $_POST['testimonial_id']);
      if ($testimonial) {
        $testimonial['status'] = $testimonial['status'] === 'active' ? 'inactive' : 'active';
        $testimonialService->update((int) $testimonial['id'], $testimonial);
        Session::flash('message', $testimonial['status'] === 'active' ? 'Testimonial published to the homepage.' : 'Testimonial unpublished.');
      }
    } elseif ($action === 'add_appointment') {
      $appointmentService->create([
        'client_id' => $id,
        'appointment_date' => trim($_POST['appointment_date'] ?? ''),
        'purpose' => trim($_POST['purpose'] ?? '') ?: null,
        'notes' => trim($_POST['notes'] ?? '') ?: null,
      ]);
      Session::flash('message', 'Appointment scheduled — client notified by email.');
    } elseif ($action === 'update_appointment_status') {
      $appointmentService->update((int) $_POST['appointment_id'], [
        'appointment_date' => $_POST['appointment_date'],
        'purpose' => $_POST['purpose'] ?? null,
        'status' => $_POST['status'] ?? 'scheduled',
        'notes' => $_POST['notes'] ?? null,
      ]);
      Session::flash('message', 'Appointment updated.');
    } elseif ($action === 'delete_appointment') {
      $appointmentService->delete((int) $_POST['appointment_id']);
      Session::flash('message', 'Appointment removed.');
    } elseif ($action === 'add_travel') {
      $travelService->create([
        'client_id' => $id,
        'destination' => trim($_POST['destination'] ?? '') ?: null,
        'travel_date' => trim($_POST['travel_date'] ?? ''),
        'return_date' => trim($_POST['return_date'] ?? '') ?: null,
        'notes' => trim($_POST['notes'] ?? '') ?: null,
      ]);
      Session::flash('message', 'Trip added to travel schedule.');
    } elseif ($action === 'update_travel_status') {
      $travelService->update((int) $_POST['travel_id'], [
        'destination' => $_POST['destination'] ?? null,
        'travel_date' => $_POST['travel_date'],
        'return_date' => $_POST['return_date'] ?? null,
        'status' => $_POST['status'] ?? 'planned',
        'notes' => $_POST['notes'] ?? null,
      ]);
      Session::flash('message', 'Trip updated.');
    } elseif ($action === 'delete_travel') {
      $travelService->delete((int) $_POST['travel_id']);
      Session::flash('message', 'Trip removed.');
    } elseif ($action === 'add_follow_up') {
      $followUpService->create([
        'client_id' => $id,
        'follow_up_date' => trim($_POST['follow_up_date'] ?? ''),
        'note' => trim($_POST['note'] ?? '') ?: null,
      ]);
      Session::flash('message', 'Follow-up added.');
    } elseif ($action === 'toggle_follow_up') {
      $followUpService->toggleDone((int) $_POST['follow_up_id']);
      Session::flash('message', 'Follow-up updated.');
    } elseif ($action === 'delete_follow_up') {
      $followUpService->delete((int) $_POST['follow_up_id']);
      Session::flash('message', 'Follow-up removed.');
    }
  } catch (\RuntimeException $e) {
    Session::flash('error', $e->getMessage());
  }

  header('Location: clients.php');
  exit;
}

$items = $service->getAll();
$pageTitle = 'Clients';
$currentAdminPage = 'clients';
include __DIR__ . '/includes/header.php';
?>
<div class="content-card p-4 mb-4">
  <h2 class="mb-3">Register New Client</h2>
  <?php if ($message): ?><div class="alert alert-success"><?php echo htmlspecialchars($message); ?></div><?php endif; ?>
  <?php if ($error): ?><div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div><?php endif; ?>
  <form method="POST" enctype="multipart/form-data" class="row g-2">
    <input type="hidden" name="csrf" value="<?php echo Session::csrfToken(); ?>">
    <input type="hidden" name="action" value="register">
    <div class="col-md-3"><input class="form-control" name="full_name" placeholder="Full name" required></div>
    <div class="col-md-3"><input class="form-control" type="email" name="email" placeholder="Email"></div>
    <div class="col-md-2"><input class="form-control" name="phone" placeholder="Phone"></div>
    <div class="col-md-2"><input class="form-control" name="country_of_origin" placeholder="Country of origin"></div>
    <div class="col-md-2"><input class="form-control" name="purpose_of_visit" placeholder="Purpose of visit"></div>
    <div class="col-md-4">
      <label class="form-label small text-muted mb-0">Profile picture</label>
      <input class="form-control" type="file" name="profile_picture" accept="image/*">
    </div>
    <div class="col-md-3">
      <label class="form-label small text-muted mb-0">Status</label>
      <select class="form-select" name="status">
        <option value="scheduled">Scheduled</option>
        <option value="completed">Completed</option>
        <option value="cancelled">Cancelled</option>
      </select>
    </div>
    <div class="col-md-3">
      <label class="form-label small text-muted mb-0">Scheduled date</label>
      <input type="date" class="form-control" name="scheduled_date">
    </div>
    <div class="col-md-2 d-flex align-items-end"><button class="btn btn-success w-100">Register</button></div>
  </form>
</div>

<div class="content-card p-4">
  <h2 class="mb-3">Clients</h2>
  <p class="text-muted small">Clients are created automatically when you accept an enquiry, or manually with the form above.</p>
  <table class="table table-sm align-middle">
    <thead><tr><th></th><th>Since</th><th>Name</th><th>Email</th><th>Phone</th><th>Interested In</th><th>Status / Scheduled Date</th><th></th></tr></thead>
    <tbody>
      <?php foreach ($items as $item): ?>
      <tr>
        <td>
          <?php if (!empty($item['profile_picture'])): ?>
          <img src="../<?php echo htmlspecialchars($item['profile_picture']); ?>" style="width:44px;height:44px;object-fit:cover;border-radius:50%;">
          <?php else: ?>
          <div class="d-flex align-items-center justify-content-center bg-light text-muted rounded-circle" style="width:44px;height:44px;font-size:.75rem;">N/A</div>
          <?php endif; ?>
        </td>
        <td><?php echo htmlspecialchars($item['created_at']); ?></td>
        <td><?php echo htmlspecialchars($item['full_name']); ?></td>
        <td><?php echo htmlspecialchars($item['email'] ?? ''); ?></td>
        <td><?php echo htmlspecialchars($item['phone'] ?? ''); ?></td>
        <td><?php echo htmlspecialchars($item['interest'] ?? ''); ?></td>
        <td class="d-flex gap-1">
          <form method="POST" class="d-flex gap-1">
            <input type="hidden" name="csrf" value="<?php echo Session::csrfToken(); ?>">
            <input type="hidden" name="action" value="update_status">
            <input type="hidden" name="id" value="<?php echo (int) $item['id']; ?>">
            <select class="form-select form-select-sm" name="status">
              <option value="scheduled" <?php echo $item['status'] === 'scheduled' ? 'selected' : ''; ?>>Scheduled</option>
              <option value="completed" <?php echo $item['status'] === 'completed' ? 'selected' : ''; ?>>Completed</option>
              <option value="cancelled" <?php echo $item['status'] === 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
            </select>
            <input type="date" class="form-control form-control-sm" name="scheduled_date" value="<?php echo htmlspecialchars($item['scheduled_date'] ?? ''); ?>" style="width: 145px;">
            <button class="btn btn-sm btn-primary">Save</button>
          </form>
        </td>
        <td>
          <form method="POST" onsubmit="return confirm('Remove this client?');">
            <input type="hidden" name="csrf" value="<?php echo Session::csrfToken(); ?>">
            <input type="hidden" name="action" value="delete">
            <input type="hidden" name="id" value="<?php echo (int) $item['id']; ?>">
            <button class="btn btn-sm btn-outline-danger">Remove</button>
          </form>
        </td>
      </tr>
      <tr>
        <td colspan="8" class="pt-0">
          <details>
            <?php
              $clientTestimonials = $testimonialService->getForClient((int) $item['id']);
              $clientAppointments = $appointmentService->allForClient((int) $item['id']);
              $clientTravels = $travelService->allForClient((int) $item['id']);
              $clientFollowUps = $followUpService->allForClient((int) $item['id']);
            ?>
            <summary class="text-muted small">Profile, appointments, travel &amp; follow-ups</summary>

            <div class="row g-3 my-2">
              <div class="col-md-6">
                <label class="small text-muted mb-1 d-block fw-bold">Edit Profile</label>
                <form method="POST" enctype="multipart/form-data" class="row g-2">
                  <input type="hidden" name="csrf" value="<?php echo Session::csrfToken(); ?>">
                  <input type="hidden" name="action" value="update_profile">
                  <input type="hidden" name="id" value="<?php echo (int) $item['id']; ?>">
                  <div class="col-md-6"><input class="form-control form-control-sm" name="full_name" value="<?php echo htmlspecialchars($item['full_name']); ?>" placeholder="Full name"></div>
                  <div class="col-md-6"><input class="form-control form-control-sm" type="email" name="email" value="<?php echo htmlspecialchars($item['email'] ?? ''); ?>" placeholder="Email"></div>
                  <div class="col-md-6"><input class="form-control form-control-sm" name="phone" value="<?php echo htmlspecialchars($item['phone'] ?? ''); ?>" placeholder="Phone"></div>
                  <div class="col-md-6"><input class="form-control form-control-sm" name="country_of_origin" value="<?php echo htmlspecialchars($item['country_of_origin'] ?? ''); ?>" placeholder="Country of origin"></div>
                  <div class="col-md-12"><input class="form-control form-control-sm" name="purpose_of_visit" value="<?php echo htmlspecialchars($item['purpose_of_visit'] ?? ''); ?>" placeholder="Purpose of visit"></div>
                  <div class="col-md-8"><input class="form-control form-control-sm" type="file" name="profile_picture" accept="image/*"></div>
                  <div class="col-md-4"><button class="btn btn-sm btn-outline-primary w-100">Save Profile</button></div>
                </form>
              </div>

              <div class="col-md-6">
                <label class="small text-muted mb-1 d-block fw-bold">Follow-ups</label>
                <?php foreach ($clientFollowUps as $f): ?>
                <div class="d-flex align-items-center gap-2 mb-1 small">
                  <span class="<?php echo $f['is_done'] ? 'text-decoration-line-through text-muted' : ''; ?>">
                    <?php echo htmlspecialchars($f['follow_up_date']); ?> — <?php echo htmlspecialchars($f['note'] ?? ''); ?>
                  </span>
                  <form method="POST" class="d-inline">
                    <input type="hidden" name="csrf" value="<?php echo Session::csrfToken(); ?>">
                    <input type="hidden" name="action" value="toggle_follow_up">
                    <input type="hidden" name="follow_up_id" value="<?php echo (int) $f['id']; ?>">
                    <input type="hidden" name="id" value="<?php echo (int) $item['id']; ?>">
                    <button class="btn btn-sm btn-outline-secondary py-0 px-1"><?php echo $f['is_done'] ? 'Undo' : 'Done'; ?></button>
                  </form>
                  <form method="POST" class="d-inline" onsubmit="return confirm('Delete this follow-up?');">
                    <input type="hidden" name="csrf" value="<?php echo Session::csrfToken(); ?>">
                    <input type="hidden" name="action" value="delete_follow_up">
                    <input type="hidden" name="follow_up_id" value="<?php echo (int) $f['id']; ?>">
                    <input type="hidden" name="id" value="<?php echo (int) $item['id']; ?>">
                    <button class="btn btn-sm btn-outline-danger py-0 px-1">&times;</button>
                  </form>
                </div>
                <?php endforeach; ?>
                <?php if (empty($clientFollowUps)): ?><p class="text-muted small mb-2">No follow-ups yet.</p><?php endif; ?>
                <form method="POST" class="d-flex gap-1">
                  <input type="hidden" name="csrf" value="<?php echo Session::csrfToken(); ?>">
                  <input type="hidden" name="action" value="add_follow_up">
                  <input type="hidden" name="id" value="<?php echo (int) $item['id']; ?>">
                  <input type="date" class="form-control form-control-sm" name="follow_up_date" required style="max-width:150px;">
                  <input class="form-control form-control-sm" name="note" placeholder="Note">
                  <button class="btn btn-sm btn-outline-success">Add</button>
                </form>
              </div>
            </div>

            <div class="row g-3 my-2">
              <div class="col-md-6">
                <label class="small text-muted mb-1 d-block fw-bold">Appointments</label>
                <?php foreach ($clientAppointments as $a): ?>
                <div class="d-flex align-items-center gap-2 mb-1 small flex-wrap">
                  <span><?php echo htmlspecialchars(date('M j, Y g:i A', strtotime($a['appointment_date']))); ?></span>
                  <span class="text-muted"><?php echo htmlspecialchars($a['purpose'] ?? ''); ?></span>
                  <form method="POST" class="d-flex gap-1">
                    <input type="hidden" name="csrf" value="<?php echo Session::csrfToken(); ?>">
                    <input type="hidden" name="action" value="update_appointment_status">
                    <input type="hidden" name="appointment_id" value="<?php echo (int) $a['id']; ?>">
                    <input type="hidden" name="id" value="<?php echo (int) $item['id']; ?>">
                    <input type="hidden" name="appointment_date" value="<?php echo htmlspecialchars($a['appointment_date']); ?>">
                    <input type="hidden" name="purpose" value="<?php echo htmlspecialchars($a['purpose'] ?? ''); ?>">
                    <input type="hidden" name="notes" value="<?php echo htmlspecialchars($a['notes'] ?? ''); ?>">
                    <select class="form-select form-select-sm" name="status" onchange="this.form.submit()" style="width:120px;">
                      <option value="scheduled" <?php echo $a['status'] === 'scheduled' ? 'selected' : ''; ?>>Scheduled</option>
                      <option value="completed" <?php echo $a['status'] === 'completed' ? 'selected' : ''; ?>>Completed</option>
                      <option value="cancelled" <?php echo $a['status'] === 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                    </select>
                  </form>
                  <form method="POST" onsubmit="return confirm('Delete this appointment?');">
                    <input type="hidden" name="csrf" value="<?php echo Session::csrfToken(); ?>">
                    <input type="hidden" name="action" value="delete_appointment">
                    <input type="hidden" name="appointment_id" value="<?php echo (int) $a['id']; ?>">
                    <input type="hidden" name="id" value="<?php echo (int) $item['id']; ?>">
                    <button class="btn btn-sm btn-outline-danger py-0 px-1">&times;</button>
                  </form>
                </div>
                <?php endforeach; ?>
                <?php if (empty($clientAppointments)): ?><p class="text-muted small mb-2">No appointments yet.</p><?php endif; ?>
                <form method="POST" class="d-flex gap-1 flex-wrap">
                  <input type="hidden" name="csrf" value="<?php echo Session::csrfToken(); ?>">
                  <input type="hidden" name="action" value="add_appointment">
                  <input type="hidden" name="id" value="<?php echo (int) $item['id']; ?>">
                  <input type="datetime-local" class="form-control form-control-sm" name="appointment_date" required style="max-width:190px;">
                  <input class="form-control form-control-sm" name="purpose" placeholder="Purpose" style="max-width:140px;">
                  <input class="form-control form-control-sm" name="notes" placeholder="Notes" style="max-width:140px;">
                  <button class="btn btn-sm btn-outline-success">Schedule</button>
                </form>
              </div>

              <div class="col-md-6">
                <label class="small text-muted mb-1 d-block fw-bold">Travel Schedule</label>
                <?php foreach ($clientTravels as $t): ?>
                <div class="d-flex align-items-center gap-2 mb-1 small flex-wrap">
                  <span><?php echo htmlspecialchars($t['destination'] ?? ''); ?></span>
                  <span class="text-muted"><?php echo htmlspecialchars($t['travel_date']); ?><?php echo $t['return_date'] ? ' → ' . htmlspecialchars($t['return_date']) : ''; ?></span>
                  <form method="POST" class="d-flex gap-1">
                    <input type="hidden" name="csrf" value="<?php echo Session::csrfToken(); ?>">
                    <input type="hidden" name="action" value="update_travel_status">
                    <input type="hidden" name="travel_id" value="<?php echo (int) $t['id']; ?>">
                    <input type="hidden" name="id" value="<?php echo (int) $item['id']; ?>">
                    <input type="hidden" name="destination" value="<?php echo htmlspecialchars($t['destination'] ?? ''); ?>">
                    <input type="hidden" name="travel_date" value="<?php echo htmlspecialchars($t['travel_date']); ?>">
                    <input type="hidden" name="return_date" value="<?php echo htmlspecialchars($t['return_date'] ?? ''); ?>">
                    <input type="hidden" name="notes" value="<?php echo htmlspecialchars($t['notes'] ?? ''); ?>">
                    <select class="form-select form-select-sm" name="status" onchange="this.form.submit()" style="width:120px;">
                      <option value="planned" <?php echo $t['status'] === 'planned' ? 'selected' : ''; ?>>Planned</option>
                      <option value="confirmed" <?php echo $t['status'] === 'confirmed' ? 'selected' : ''; ?>>Confirmed</option>
                      <option value="completed" <?php echo $t['status'] === 'completed' ? 'selected' : ''; ?>>Completed</option>
                      <option value="cancelled" <?php echo $t['status'] === 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                    </select>
                  </form>
                  <form method="POST" onsubmit="return confirm('Delete this trip?');">
                    <input type="hidden" name="csrf" value="<?php echo Session::csrfToken(); ?>">
                    <input type="hidden" name="action" value="delete_travel">
                    <input type="hidden" name="travel_id" value="<?php echo (int) $t['id']; ?>">
                    <input type="hidden" name="id" value="<?php echo (int) $item['id']; ?>">
                    <button class="btn btn-sm btn-outline-danger py-0 px-1">&times;</button>
                  </form>
                </div>
                <?php endforeach; ?>
                <?php if (empty($clientTravels)): ?><p class="text-muted small mb-2">No trips scheduled yet.</p><?php endif; ?>
                <form method="POST" class="d-flex gap-1 flex-wrap">
                  <input type="hidden" name="csrf" value="<?php echo Session::csrfToken(); ?>">
                  <input type="hidden" name="action" value="add_travel">
                  <input type="hidden" name="id" value="<?php echo (int) $item['id']; ?>">
                  <input class="form-control form-control-sm" name="destination" placeholder="Destination" style="max-width:140px;">
                  <input type="date" class="form-control form-control-sm" name="travel_date" required style="max-width:150px;">
                  <input type="date" class="form-control form-control-sm" name="return_date" style="max-width:150px;">
                  <input class="form-control form-control-sm" name="notes" placeholder="Notes" style="max-width:140px;">
                  <button class="btn btn-sm btn-outline-success">Add Trip</button>
                </form>
              </div>
            </div>

            <div class="my-2">
              <label class="small text-muted mb-1 d-block fw-bold">Testimonial (<?php echo count($clientTestimonials); ?>)</label>
              <?php foreach ($clientTestimonials as $t): ?>
              <div class="d-flex align-items-start gap-2 my-2 p-2 border rounded">
                <div class="flex-grow-1">
                  <div class="fst-italic small">"<?php echo htmlspecialchars($t['quote']); ?>"</div>
                  <span class="badge bg-<?php echo $t['status'] === 'active' ? 'success' : 'secondary'; ?>"><?php echo $t['status'] === 'active' ? 'Live on homepage' : 'Pending review'; ?></span>
                </div>
                <form method="POST">
                  <input type="hidden" name="csrf" value="<?php echo Session::csrfToken(); ?>">
                  <input type="hidden" name="action" value="toggle_testimonial">
                  <input type="hidden" name="testimonial_id" value="<?php echo (int) $t['id']; ?>">
                  <input type="hidden" name="id" value="<?php echo (int) $item['id']; ?>">
                  <button class="btn btn-sm <?php echo $t['status'] === 'active' ? 'btn-outline-secondary' : 'btn-outline-success'; ?>"><?php echo $t['status'] === 'active' ? 'Unpublish' : 'Publish'; ?></button>
                </form>
              </div>
              <?php endforeach; ?>

              <form method="POST" class="d-flex flex-column gap-2 my-2" style="max-width:500px;">
                <input type="hidden" name="csrf" value="<?php echo Session::csrfToken(); ?>">
                <input type="hidden" name="action" value="add_testimonial">
                <input type="hidden" name="id" value="<?php echo (int) $item['id']; ?>">
                <textarea class="form-control form-control-sm" name="quote" rows="2" placeholder="What did this client say about their trip?" required></textarea>
                <input class="form-control form-control-sm" name="author_location" placeholder="Shown under their name (defaults to their interest)">
                <button class="btn btn-sm btn-outline-primary align-self-start">Submit Testimonial</button>
              </form>
            </div>
          </details>
        </td>
      </tr>
      <?php endforeach; ?>
      <?php if (empty($items)): ?>
      <tr><td colspan="8" class="text-center text-muted">No clients yet.</td></tr>
      <?php endif; ?>
    </tbody>
  </table>
</div>
<?php include __DIR__ . '/includes/footer.php'; ?>
