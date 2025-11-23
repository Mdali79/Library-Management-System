# 💰 Fine Management - Role-Based Permissions

## 📋 Current Permissions by Role

### 👨‍🎓 **Student/Teacher** (Limited Access)

#### ✅ **Can Do:**
1. **View Own Fines**
   - See only their own pending, paid, and waived fines
   - View their own fine statistics (pending amount, paid amount, total)
   - See fine details: book name, days overdue, amount, status, payment method

2. **Pay Own Fines**
   - Pay their own pending fines
   - Choose payment method (Cash or Online)
   - Add payment notes

#### ❌ **Cannot Do:**
- ❌ View other students' fines
- ❌ Waive fines (even their own)
- ❌ Calculate overdue fines
- ❌ Filter by student ID
- ❌ Access fine history of other students
- ❌ Pay fines for other students

---

### 👨‍💼 **Librarian** (Moderate Access)

#### ✅ **Can Do:**
1. **View All Fines**
   - See fines for all students
   - View system-wide statistics (all pending, paid, total fines)
   - Filter fines by status (pending, paid, waived)
   - Filter fines by student ID

2. **Process Payments**
   - Pay fines for any student
   - Record payment method (Cash or Online)
   - Add payment notes

3. **Manage Fines**
   - **Waive fines** for any student
   - **Calculate overdue fines** (auto-generate fines for overdue books)
   - View fine history

#### ❌ **Cannot Do:**
- ❌ Modify fine settings (Admin only)
- ❌ Delete fine records

---

### 🔐 **Admin** (Full Access)

#### ✅ **Can Do:**
1. **View All Fines**
   - See fines for all students
   - View system-wide statistics (all pending, paid, total fines)
   - Filter fines by status (pending, paid, waived)
   - Filter fines by student ID
   - Access comprehensive fine history

2. **Process Payments**
   - Pay fines for any student
   - Record payment method (Cash or Online)
   - Add payment notes

3. **Manage Fines**
   - **Waive fines** for any student
   - **Calculate overdue fines** (auto-generate fines for overdue books)
   - View fine history
   - Export fine reports

4. **System Management**
   - Configure fine settings (fine per day, grace period)
   - Manage fine-related system settings

---

## 🔒 Security Considerations

### Current Issues:
1. ⚠️ **No Role Checks in Controller**: Methods like `waive()` and `calculateOverdueFines()` don't check user role
2. ⚠️ **View Shows All Buttons**: Students can see "Waive" and "Calculate Overdue" buttons (though they may not work)
3. ⚠️ **Payment Validation**: Students can potentially pay fines for others if they know the fine ID

### Recommended Restrictions:
- ✅ Students should only pay their own fines
- ✅ Only Admin/Librarian can waive fines
- ✅ Only Admin/Librarian can calculate overdue fines
- ✅ View should hide restricted buttons based on role

---

## 📊 Fine Status Flow

```
Pending → Paid (via payment)
Pending → Waived (via admin/librarian action)
```

**Note**: Once paid or waived, fine cannot be modified.

---

## 🎯 Fine Calculation Logic

### Overdue Fine Calculation:
1. **Grace Period**: No fine for first X days (configurable in settings)
2. **Daily Rate**: Fine per day after grace period (configurable)
3. **Formula**: `Fine = (Days Overdue - Grace Period) × Fine Per Day`
4. **Auto-Calculation**: Admin/Librarian can trigger calculation for all overdue books

---

## 📝 Actions Summary Table

| Action | Student | Teacher | Librarian | Admin |
|--------|---------|---------|-----------|-------|
| View Own Fines | ✅ | ✅ | ✅ | ✅ |
| View All Fines | ❌ | ❌ | ✅ | ✅ |
| Pay Own Fines | ✅ | ✅ | ✅ | ✅ |
| Pay Any Fines | ❌ | ❌ | ✅ | ✅ |
| Waive Fines | ❌ | ❌ | ✅ | ✅ |
| Calculate Overdue | ❌ | ❌ | ✅ | ✅ |
| Filter by Student | ❌ | ❌ | ✅ | ✅ |
| View Statistics | Own Only | Own Only | All | All |
| Export Reports | ❌ | ❌ | ✅ | ✅ |

---

## 🔄 Workflow Examples

### Student Paying Fine:
1. Student logs in
2. Goes to "Fines" menu
3. Sees only their pending fines
4. Clicks "Pay" button on a fine
5. Selects payment method
6. Confirms payment
7. Fine status changes to "Paid"

### Librarian Waiving Fine:
1. Librarian logs in
2. Goes to "Fines" menu
3. Sees all students' fines
4. Finds a fine to waive
5. Clicks "Waive" button
6. Confirms waiver
7. Fine status changes to "Waived"

### Admin Calculating Overdue Fines:
1. Admin logs in
2. Goes to "Fines" menu
3. Clicks "Calculate Overdue Fines" button
4. System scans all overdue books
5. Creates fine records for overdue books
6. Shows success message with count

---

## 🛡️ Security Best Practices

1. **Role-Based Access Control**: Always check user role before allowing actions
2. **Ownership Validation**: Students can only access/modify their own fines
3. **Audit Trail**: Track who waived/paid fines and when
4. **Input Validation**: Validate all payment amounts and methods
5. **View Restrictions**: Hide buttons/actions users cannot perform

---

## 📌 Notes

- Fine calculation is **manual** (triggered by Admin/Librarian)
- Fines are created when books are overdue beyond grace period
- Payment can be recorded as Cash or Online
- Waived fines cannot be undone
- Fine history is maintained for reporting

