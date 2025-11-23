# 📅 Fine Grace Period Configuration Guide

## 🎯 What is Fine Grace Period?

The **Fine Grace Period** is the number of days **after the return date** before fine calculation starts.

### Example:
- **Return Date**: January 1, 2024
- **Grace Period**: 14 days
- **Fine Calculation Starts**: January 15, 2024 (14 days after return date)
- **Fine Charged**: Only for days AFTER January 15

---

## 📍 Where to Configure

### Location:
1. Login as **Admin** or **Librarian**
2. Go to **Settings** (in the navigation menu)
3. Find **"Fine Grace Period (Days)"** field
4. Enter the number of days (0-30)
5. Click **"Update Settings"**

### Path:
```
Dashboard → Settings → Fine Grace Period (Days)
```

---

## ⚙️ How It Works

### Fine Calculation Formula:
```
Days Overdue = Current Date - Return Date
Chargeable Days = Days Overdue - Grace Period
Fine Amount = Chargeable Days × Fine Per Day
```

### Example Calculation:

**Scenario:**
- Return Date: January 1, 2024
- Grace Period: 14 days
- Fine Per Day: $1.00
- Current Date: January 20, 2024

**Calculation:**
1. Days Overdue = 20 - 1 = **19 days**
2. Chargeable Days = 19 - 14 = **5 days**
3. Fine Amount = 5 × $1.00 = **$5.00**

**Result:** Student pays $5.00 (only for 5 days after grace period)

---

## 📊 Grace Period Examples

### Grace Period = 0 days
- Fine starts **immediately** after return date
- No grace period
- Every overdue day is charged

### Grace Period = 7 days
- Fine starts **7 days** after return date
- First 7 days are free
- Days 8+ are charged

### Grace Period = 14 days (Default)
- Fine starts **14 days** after return date
- First 14 days are free
- Days 15+ are charged

### Grace Period = 30 days (Maximum)
- Fine starts **30 days** after return date
- First 30 days are free
- Days 31+ are charged

---

## 🔧 Configuration Steps

### Step 1: Access Settings
1. Login as Admin or Librarian
2. Click **"Settings"** in the navigation menu

### Step 2: Find Fine Grace Period
1. Scroll to **"Return & Fine Settings"** section
2. Find **"Fine Grace Period (Days)"** field

### Step 3: Set Value
1. Enter number of days (0-30)
2. Recommended: **7-14 days** for most libraries

### Step 4: Save
1. Click **"Update Settings"** button
2. Settings saved immediately

---

## ⚠️ Important Notes

### 1. **Grace Period vs Return Days**
- **Return Days**: How long a book can be borrowed (e.g., 14 days)
- **Grace Period**: Days after return date before fine starts (e.g., 14 days)

**Example:**
- Book issued: Jan 1
- Return Days: 14 → Return Date: Jan 15
- Grace Period: 7 days
- Fine starts: Jan 22 (7 days after Jan 15)

### 2. **Fine Calculation**
- Fines are **NOT automatically calculated**
- Admin/Librarian must click **"Calculate Overdue Fines"** button
- This creates fine records for overdue books

### 3. **Zero Grace Period**
- If set to 0, fine starts immediately after return date
- Every overdue day is charged
- No free days

### 4. **Maximum Grace Period**
- Maximum allowed: 30 days
- Recommended: 7-14 days for balance

---

## 📋 Settings Page Fields

### Return & Fine Settings Section:
1. **Return Days**: How long books can be borrowed
2. **Fine Per Day**: Amount charged per day after grace period
3. **Fine Grace Period (Days)**: Days after return date before fine starts ⭐

---

## 🎯 Best Practices

### Recommended Grace Period:
- **Small Libraries**: 7 days
- **Medium Libraries**: 14 days (default)
- **Large Libraries**: 14-21 days

### Considerations:
- ✅ Too short (0-3 days): May be too strict
- ✅ Too long (21-30 days): May reduce urgency to return
- ✅ Balanced (7-14 days): Good balance between flexibility and accountability

---

## 🔄 After Configuration

### To Apply Fine Calculation:
1. Go to **"Fines"** menu
2. Click **"Calculate Overdue Fines"** button
3. System scans all overdue books
4. Creates fine records for books past grace period
5. Shows count of fines created

---

## 📍 Quick Reference

| Setting | Location | Default | Range |
|---------|----------|---------|-------|
| Fine Grace Period | Settings → Fine Grace Period (Days) | 14 days | 0-30 days |
| Fine Per Day | Settings → Fine Per Day | $0.00 | Any amount |
| Return Days | Settings → Return Days | 14 days | 1-365 days |

---

## ✅ Summary

**Where to Set:** Settings → Fine Grace Period (Days)

**What it does:** Number of days after return date before fine calculation starts

**Default:** 14 days

**Range:** 0-30 days

**Access:** Admin and Librarian only

---

**Configure it now in Settings!** ⚙️

