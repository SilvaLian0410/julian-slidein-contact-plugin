# Julian Slide-In Lead Capture

This WordPress plugin delivers a lightweight slide-in form that gathers a visitor’s first name, last name, and email address. It helps TechWorms (and any network of sites) increase return visitors by collecting contact details in a friendly, unobtrusive way while enforcing the project’s two critical requirements:

1. **Spam protection:** Google reCAPTCHA v2 must be solved before a submission is accepted.
2. **Thoughtful timing:** The panel auto-opens one time, 10 seconds after the very first page a visitor loads, then stays hidden for the rest of the session and for the next 24 hours.

All responses are saved to the WordPress database and visible inside the admin area, so editors can follow up without leaving the dashboard.
---

## Screenshots
<img width="1685" height="1390" alt="Screenshot 2025-11-17 165945" src="https://github.com/user-attachments/assets/49568a2d-fdc0-402f-bc6a-159308e6b896" />
<img width="1577" height="1407" alt="Screenshot 2025-11-17 165956" src="https://github.com/user-attachments/assets/136a40d9-c57b-4b08-a559-e37d8b33f1bb" />
<img width="809" height="1265" alt="Screenshot 2025-11-17 170015" src="https://github.com/user-attachments/assets/2a42f6b4-61c8-4618-a49e-ffa7ff1fb42a" />
<img width="508" height="124" alt="image" src="https://github.com/user-attachments/assets/66671720-3281-4a70-b1b7-5eb1b4e8daae" />



---

## Requirements & Assumptions
- WordPress 5.3 or newer (tested with classic themes and block themes).
- PHP 7.2+ with cURL enabled (needed for the reCAPTCHA verification call).
- Google reCAPTCHA v2 Checkbox keys (site + secret).  
  *For testing, Google provides keys that always pass the challenge:*  
  *Site* `6LeIxAcTAAAAAJcZVRqyHh71UMIEGNQ_MXjiZKhI`  
  *Secret* `6LeIxAcTAAAAAGG-vFI1TnRWxMZNFuojJ4WifJWe`

---

## Installation (No Coding Needed)
1. **Download the plugin folder** (or the provided zip) to your computer.
2. In your WordPress dashboard go to `Plugins → Add New → Upload Plugin`.
3. Click **Choose File**, select `julian-slide-in.zip`, then press **Install Now**.
4. When WordPress finishes unpacking, click **Activate Plugin**.
5. A new menu named **Julian Slide-In** will appear on the left-hand sidebar.

Alternatively, you can upload the `julian-slide-in` folder to `wp-content/plugins/` via FTP and activate it under `Plugins`.

---

## Configuration Guide
1. Navigate to **Julian Slide-In → Settings**.
2. Work through the options from top to bottom:
   - **Enabled:** Toggle the slide-in on/off.
   - **Position:** Choose whether it slides in from the left or right.
   - **Colors:** Pick your primary/accent colors plus dedicated background and text colors for the panel.
   - **Trigger Button Label:** Used when the launcher style is set to “Button”.
   - **Launcher Icon:** Use the built-in dropdown to pick a Font Awesome icon—no need to visit the Font Awesome website.
   - **Send To Email:** The address that receives lead notifications.
   - **Google reCAPTCHA:** Enter your site and secret keys (required for live use).
3. Click **Save Changes**.

---

## Behaviour Details
- **Auto Slide-In Timing:** On a visitor’s first page view the script waits exactly 10 seconds, then opens the panel once. It sets a flag in `sessionStorage` and a timestamp in `localStorage` so it will not auto-open again for that session or for the next 24 hours—even if the visitor browses additional pages.
- **Spam Protection:** If reCAPTCHA is enabled and/or required keys are missing, the plugin blocks submission and displays an error. A valid token must be generated and verified using Google’s API before data is stored or emails are sent.
- **Submission Handling:** Each entry is sanitized, emailed to the configured address, and recorded in the `wp_jsi_entries` table together with the landing page URL and timestamp.

---

## Viewing Leads
- Go to **Julian Slide-In → Submissions** to see all stored entries. The table lists the capture date, the visitor’s names, email (with a mailto link), and the page URL they were on when the slide-in appeared.
- Pagination is built in (20 entries per page). If the table ever disappears—say, after a manual database cleanup—simply deactivate and reactivate the plugin to recreate it.

---

## Troubleshooting
- **reCAPTCHA errors:** Double-check that the domain registered with Google matches your site and that both keys are entered correctly. For staging sites, use the universal test keys listed above.
- **No emails arriving:** Confirm the “Send To Email” address is valid and make sure your WordPress site can send mail (consider configuring SMTP for reliable delivery).
- **Slide-in not appearing:** Ensure the plugin is enabled, no JavaScript errors are present, and that you haven’t recently triggered the 24-hour suppression. Clearing localStorage/sessionStorage or visiting in a private browser window lets you test the 10-second behavior again.

Need more adjustments—different fields, export tools, or analytics hooks? Add issues or reach out to your developer; the codebase is organized so new features can be slotted into the existing `includes` and `assets` directories.
