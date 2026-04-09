# LRS Demo Guide: Document Lifecycle Walkthrough

This guide provides a structured script for demonstrating the primary document recording lifecycle within the Laravel Recording System.

---

## 🎬 Act 1: The Intake (Admin/Operations)
1. **Login**: Sign in as an Administrative user.
2. **Setup**: Briefly show the **Masters** section (Clients, Counties) to explain how the database is configured.
3. **Check-In**: Navigate to `Files > Create New File`.
   - Entry: Select a Client and Document Type.
   - Page Count: Enter `5`.
   - Result: Show the automatically generated **LRS Tracking Number** and the **System-Calculated Recording Fees**.

---

## 🔍 Act 2: The Quality Gate (QC Clerk)
1. **Navigation**: Go to the **QC Module** link in the top-bar.
2. **Review**: Open the newly created file.
3. **Verification**: 
   - Click **"Mark as QC Passed"**.
   - Note: Mention that the file is now locked for everyone except Accounting.

---

## ⚖️ Act 3: Financial Clearance (Accounting)
1. **Accounting Queue**: Navigate to the **Accounting** link.
2. **Audit**: 
   - Show how the Fee Rules matched the document.
   - Click **"Approve Fees"**.
3. **Outcome**: The file status moves to **Fees Approved**, enabling the physical shipping workflow.

---

## 📦 Act 4: Logistics & Closure (Operations)
1. **Shipping**: Go to `Post-Closing > Shipping`.
   - Select the file and click **"Generate Manifest"**. 
   - Note: Show how the system captures Courier tracking.
2. **Recording**: Navigate to `Post-Closing > Recording`.
   - Record the legal identifiers: **Instrument #**, **Book**, and **Page**.
3. **Final Return**: Go to `Post-Closing > Returns`.
   - Click **"Close File & Return to Partner"**.
   - Result: File is now in the **Closed** terminal state.

---

## 📊 Act 5: Executive Intelligence (Reports)
1. **Dashboard**: Navigate to **Operational Intelligence**.
2. **Key Visuals**:
   - Show the **Daily Heartbeat** showing today's throughput.
   - Show the **Aging Bottlenecks** to see if any files are stuck.
   - **Filter**: Set the filter to the current month to demonstrate pivot reporting.
3. **The Payoff**: Click **"Export CSV"** to demonstrate one-click auditing for the entire department.

---

## 💡 Key Talking Points for Demo
- **Automation**: "Every fee is calculated in 0.2 seconds based on county rules."
- **Traceability**: "We know exactly who passed QC and which courier has the documents."
- **Scalability**: "The Import Service can ingest 500 files at a time without breaking the workflow."
