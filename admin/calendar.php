<?php
$adminPageTitle = "Event Schedule Calendar";
$adminActiveNav = "calendar";
require_once __DIR__ . '/includes/admin-header.php';

$pdo = getDBConnection();

// Month & Year selection
$month = intval($_GET['month'] ?? date('n'));
$year = intval($_GET['year'] ?? date('Y'));

if ($month < 1) { $month = 12; $year--; }
if ($month > 12) { $month = 1; $year++; }

$firstDayOfMonth = mktime(0, 0, 0, $month, 1, $year);
$numberDays = date('t', $firstDayOfMonth);
$dateComponents = getdate($firstDayOfMonth);
$monthName = $dateComponents['month'];
$dayOfWeek = $dateComponents['wday']; // 0 (Sun) to 6 (Sat)

// Fetch all bookings for this month
$startDate = sprintf("%04d-%02d-01", $year, $month);
$endDate = sprintf("%04d-%02d-%02d", $year, $month, $numberDays);

$stmt = $pdo->prepare("
    SELECT * FROM bookings
    WHERE event_date BETWEEN :start AND :end AND status != 'Cancelled'
    ORDER BY event_date ASC
");
$stmt->execute([':start' => $startDate, ':end' => $endDate]);
$monthBookings = $stmt->fetchAll();

// Group by date
$eventsByDate = [];
foreach ($monthBookings as $b) {
    $eventsByDate[$b['event_date']][] = $b;
}

$prevMonth = $month - 1;
$prevYear = $year;
$nextMonth = $month + 1;
$nextYear = $year;
?>

<div class="admin-card">
  <!-- Calendar Header Controls -->
  <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px; flex-wrap: wrap; gap: 15px;">
    <div>
      <h2 style="font-size: 22px; font-weight: 700; color: var(--admin-text);"><?php echo $monthName . ' ' . $year; ?></h2>
      <span style="color: var(--admin-muted); font-size: 13px;">Shows & Productions Scheduled: <?php echo count($monthBookings); ?></span>
    </div>
    
    <div style="display: flex; gap: 10px;">
      <a href="calendar.php?month=<?php echo $prevMonth; ?>&year=<?php echo $prevYear; ?>" class="btn-admin" style="background: #f1f5f9; color: #334155;">
        <i class="fa-solid fa-chevron-left"></i> Prev Month
      </a>
      <a href="calendar.php?month=<?php echo date('n'); ?>&year=<?php echo date('Y'); ?>" class="btn-admin" style="background: #f1f5f9; color: #334155;">
        Today
      </a>
      <a href="calendar.php?month=<?php echo $nextMonth; ?>&year=<?php echo $nextYear; ?>" class="btn-admin" style="background: #f1f5f9; color: #334155;">
        Next Month <i class="fa-solid fa-chevron-right"></i>
      </a>
    </div>
  </div>

  <!-- Calendar Grid -->
  <style>
    .calendar-grid {
      display: grid;
      grid-template-columns: repeat(7, 1fr);
      border: 1px solid var(--admin-border);
      border-radius: 8px;
      overflow: hidden;
    }
    .cal-header-day {
      background: #f8fafc;
      padding: 12px;
      text-align: center;
      font-weight: 700;
      font-size: 12px;
      color: #475569;
      border-bottom: 1px solid var(--admin-border);
      text-transform: uppercase;
    }
    .cal-day-cell {
      min-height: 110px;
      padding: 8px;
      border-right: 1px solid var(--admin-border);
      border-bottom: 1px solid var(--admin-border);
      background: #ffffff;
      transition: background 0.15s;
    }
    .cal-day-cell:nth-child(7n) {
      border-right: none;
    }
    .cal-day-cell.empty {
      background: #fafafa;
    }
    .cal-day-cell.today {
      background: #fffdf5;
    }
    .cal-day-number {
      font-size: 13px;
      font-weight: 700;
      color: #64748b;
      margin-bottom: 6px;
    }
    .cal-day-cell.today .cal-day-number {
      color: var(--admin-primary);
      display: inline-block;
      width: 24px;
      height: 24px;
      background: rgba(133,46,112,0.1);
      border-radius: 50%;
      text-align: center;
      line-height: 24px;
    }
    .cal-event-pill {
      font-size: 11px;
      padding: 4px 6px;
      border-radius: 4px;
      background: #fdf2f8;
      border-left: 3px solid var(--admin-primary);
      color: #852e70;
      margin-bottom: 4px;
      cursor: pointer;
      overflow: hidden;
      text-overflow: ellipsis;
      white-space: nowrap;
      font-weight: 500;
    }
    .cal-event-pill:hover {
      background: #fae8f5;
    }
  </style>

  <div class="calendar-grid">
    <!-- Days of Week Header -->
    <div class="cal-header-day">Sun</div>
    <div class="cal-header-day">Mon</div>
    <div class="cal-header-day">Tue</div>
    <div class="cal-header-day">Wed</div>
    <div class="cal-header-day">Thu</div>
    <div class="cal-header-day">Fri</div>
    <div class="cal-header-day">Sat</div>

    <!-- Empty cells before first day -->
    <?php for ($i = 0; $i < $dayOfWeek; $i++): ?>
      <div class="cal-day-cell empty"></div>
    <?php endfor; ?>

    <!-- Days of the Month -->
    <?php
      $currentDay = 1;
      while ($currentDay <= $numberDays):
        $currentDateStr = sprintf("%04d-%02d-%02d", $year, $month, $currentDay);
        $isToday = ($currentDateStr === date('Y-m-d'));
        $dayEvents = $eventsByDate[$currentDateStr] ?? [];
    ?>
      <div class="cal-day-cell <?php echo $isToday ? 'today' : ''; ?>">
        <div class="cal-day-number"><?php echo $currentDay; ?></div>
        <?php foreach ($dayEvents as $ev): ?>
          <a href="bookings.php?search=<?php echo urlencode($ev['reference_code']); ?>" class="cal-event-pill" title="<?php echo htmlspecialchars($ev['name'] . ' - ' . $ev['service_package']); ?>">
            <i class="fa-solid fa-masks-theater" style="font-size: 10px; margin-right: 2px;"></i>
            <?php echo htmlspecialchars($ev['name']); ?> (<?php echo htmlspecialchars($ev['service_package']); ?>)
          </a>
        <?php endforeach; ?>
      </div>
      <?php
        $currentDay++;
        $dayOfWeekCount = ($dayOfWeek + $currentDay - 1) % 7;
      ?>
    <?php endwhile; ?>

    <!-- Fill trailing cells if row not complete -->
    <?php
      $totalCells = $dayOfWeek + $numberDays;
      $remaining = (7 - ($totalCells % 7)) % 7;
      for ($i = 0; $i < $remaining; $i++):
    ?>
      <div class="cal-day-cell empty"></div>
    <?php endfor; ?>
  </div>
</div>

<?php
require_once __DIR__ . '/includes/admin-footer.php';
?>
