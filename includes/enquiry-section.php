<?php
/**
 * Shared "Send a Trip Request" enquiry block, included near the bottom of
 * every public page (above the footer). Submits to handlers/contact, which
 * uses EnquiryService — saving to the CRM (enquiries table), emailing the
 * admin over SMTP, and confirming with the customer when an email was given.
 *
 * A page may optionally set $enquiryInterest before including this file to
 * prefill the "Destination" field with context (a tour, destination, or
 * experience title) so the enquiry lands in the CRM already labelled.
 */
$enquiryInterest = $enquiryInterest ?? '';
$enquiryTravelType = $enquiryTravelType ?? '';
$enquiryOfferId = $enquiryOfferId ?? null;
$enquiryOfferLabel = $enquiryOfferLabel ?? '';
$enquiryTravelTypeOptions = ['Local Tour (Kenya)', 'International Trip', 'Safari', 'Beach Holiday', 'MICE / Corporate Travel', 'Other'];

$enquiryPath = '/' . ltrim(basename($_SERVER['PHP_SELF'] ?? 'index.php'), '/');
parse_str($_SERVER['QUERY_STRING'] ?? '', $enquiryQueryParams);
unset($enquiryQueryParams['success'], $enquiryQueryParams['error']);
$enquiryQueryClean = http_build_query($enquiryQueryParams);
$enquiryRedirect = $enquiryPath . ($enquiryQueryClean !== '' ? '?' . $enquiryQueryClean : '');
?>
<section id="trip-request" class="section-pad" style="background: var(--charcoal); color: #fff;">
  <div class="container">
    <?php if (isset($_GET['success'])): ?>
    <div class="reveal revealed" style="background: var(--green); color: #fff; padding: 20px; border-radius: var(--radius-md); margin-bottom: 32px;">
      <strong>Trip request sent!</strong> Our team will get back to you shortly.
    </div>
    <?php elseif (isset($_GET['error'])): ?>
    <div class="reveal revealed" style="background: #b42318; color: #fff; padding: 20px; border-radius: var(--radius-md); margin-bottom: 32px;">
      <strong>Unable to send your request.</strong> Please check your details and try again.
    </div>
    <?php endif; ?>

    <div class="row g-4 align-items-stretch">
      <div class="col-lg-5 reveal">
        <div class="enquiry-visual">
          <img src="assets/images/start-here.jpeg" alt="Pentagon Quest" style="width: 100%; height: 100%; object-fit: cover; position: absolute; inset: 0;">
          <div class="enquiry-visual-overlay"></div>
          <div class="enquiry-visual-content">
            <span style="color: var(--gold); font-size: 0.7rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.15em; display: block; margin-bottom: 8px;">Start Here</span>
            <h3 style="color: #fff; margin-bottom: 0;">Tell us where you want to go. We will shape the route.</h3>
          </div>
        </div>
      </div>

      <div class="col-lg-7 reveal">
        <div class="enquiry-card">
          <div class="row g-3 mb-3">
            <div class="col-md-4">
              <div class="enquiry-info-tile">
                <div class="icon-badge"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72c.127.96.361 1.903.7 2.81a2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0 1 22 16.92z"/></svg></div>
                <span>Call</span>
                <p>+254718620982 / +254726528015</p>
              </div>
            </div>
            <div class="col-md-4">
              <div class="enquiry-info-tile">
                <div class="icon-badge"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 4h16v16H4z"/><path d="M22 6 12 13 2 6"/></svg></div>
                <span>Email</span>
                <p>pentagonquest@gmail.com</p>
              </div>
            </div>
            <div class="col-md-4">
              <div class="enquiry-info-tile">
                <div class="icon-badge"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 10c0 7-9 12-9 12s-9-5-9-12a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg></div>
                <span>Offices</span>
                <p>Nairobi, Eldoret and Kericho</p>
              </div>
            </div>
          </div>

          <?php if ($enquiryOfferId): ?>
          <div style="background: var(--gold-soft); color: var(--charcoal); padding: 12px 16px; border-radius: var(--radius-md); margin-bottom: 20px; font-size: 0.9rem;">
            <strong>Claiming offer:</strong> <?php echo htmlspecialchars($enquiryOfferLabel !== '' ? $enquiryOfferLabel : 'Offer #' . $enquiryOfferId); ?>
          </div>
          <?php endif; ?>

          <form method="POST" action="handlers/contact">
            <input type="hidden" name="redirect" value="<?php echo htmlspecialchars($enquiryRedirect); ?>">
            <?php if ($enquiryOfferId): ?>
            <input type="hidden" name="offer_id" value="<?php echo (int) $enquiryOfferId; ?>">
            <?php endif; ?>
            <div class="row g-3">
              <div class="col-md-6">
                <input type="text" name="full_name" class="enquiry-input" placeholder="Your name" required>
              </div>
              <div class="col-md-6">
                <input type="text" name="contact" class="enquiry-input" placeholder="Email or phone" required>
              </div>
              <div class="col-md-6">
                <input type="text" name="destination" class="enquiry-input" placeholder="Destination" value="<?php echo htmlspecialchars($enquiryInterest); ?>">
              </div>
              <div class="col-md-6">
                <select name="travel_type" class="enquiry-input">
                  <option value="">Travel type</option>
                  <?php foreach ($enquiryTravelTypeOptions as $option): ?>
                  <option <?php echo $enquiryTravelType === $option ? 'selected' : ''; ?>><?php echo htmlspecialchars($option); ?></option>
                  <?php endforeach; ?>
                </select>
              </div>
              <div class="col-12">
                <textarea name="message" class="enquiry-input" rows="4" placeholder="Tell us what you have in mind"></textarea>
              </div>
              <div class="col-12">
                <button type="submit" class="btn-hero" style="background: var(--gold); color: var(--charcoal); width: 100%; justify-content: center; border: none;">Send Trip Request</button>
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</section>
