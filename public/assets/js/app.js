/**
 * RISE - Main Application JavaScript
 * ====================================
 */

(function() {
    'use strict';

    // ===== SIDEBAR TOGGLE =====
    document.addEventListener('DOMContentLoaded', function() {
        const sidebar = document.getElementById('sidebar');
        const toggleBtn = document.getElementById('sidebarToggle');
        const overlay = document.getElementById('sidebarOverlay');

        if (toggleBtn && sidebar) {
            toggleBtn.addEventListener('click', function() {
                if (window.innerWidth <= 768) {
                    sidebar.classList.toggle('mobile-open');
                } else {
                    sidebar.classList.toggle('collapsed');
                    localStorage.setItem('sidebar_collapsed', sidebar.classList.contains('collapsed'));
                }
            });
        }

        if (overlay) {
            overlay.addEventListener('click', function() {
                sidebar.classList.remove('mobile-open');
            });
        }

        // Restore sidebar state
        if (window.innerWidth > 768) {
            const isCollapsed = localStorage.getItem('sidebar_collapsed') === 'true';
            if (isCollapsed && sidebar) {
                sidebar.classList.add('collapsed');
            }
        }

        // ===== TOOLTIP INIT =====
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        tooltipTriggerList.map(function(el) {
            return new bootstrap.Tooltip(el);
        });

        // ===== AUTO-DISMISS ALERTS =====
        document.querySelectorAll('.alert-auto-dismiss').forEach(function(alert) {
            setTimeout(function() {
                var bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            }, 5000);
        });

        // ===== CONFIRM DELETE =====
        document.querySelectorAll('[data-confirm]').forEach(function(el) {
            el.addEventListener('click', function(e) {
                if (!confirm(this.getAttribute('data-confirm'))) {
                    e.preventDefault();
                }
            });
        });
    });

    // ===== AJAX COURSE LOADER =====
    window.loadCourses = function(programId, targetSelect, selectedId) {
        const select = document.getElementById(targetSelect);
        if (!select) return;

        select.innerHTML = '<option value="">Loading...</option>';

        if (!programId) {
            select.innerHTML = '<option value="">-- Select Course --</option>';
            return;
        }

        fetch('ajax_get_courses.php?program_id=' + encodeURIComponent(programId))
            .then(response => response.json())
            .then(data => {
                select.innerHTML = '<option value="">-- Select Course --</option>';
                data.forEach(function(course) {
                    const option = document.createElement('option');
                    option.value = course.id;
                    option.textContent = course.course_name;
                    if (selectedId && course.id == selectedId) {
                        option.selected = true;
                    }
                    select.appendChild(option);
                });
            })
            .catch(err => {
                select.innerHTML = '<option value="">Error loading courses</option>';
                console.error('Course load error:', err);
            });
    };

    // ===== AJAX SUBJECTS LOADER =====
    window.loadSubjects = function(programId, targetContainer) {
        const container = document.getElementById(targetContainer);
        if (!container) return;

        if (!programId) {
            container.innerHTML = '<p class="text-muted">Select a student to load subjects.</p>';
            return;
        }

        container.innerHTML = '<div class="text-center py-3"><div class="spinner-border spinner-border-sm text-primary"></div> Loading subjects...</div>';

        fetch('ajax_get_subjects.php?program_id=' + encodeURIComponent(programId))
            .then(response => response.json())
            .then(data => {
                if (data.length === 0) {
                    container.innerHTML = '<p class="text-warning">No subjects found for this program.</p>';
                    return;
                }

                let html = '<table class="table"><thead><tr>';
                html += '<th>Subject</th><th>Total Marks</th><th>Marks Obtained</th><th>Grade</th>';
                html += '</tr></thead><tbody>';

                data.forEach(function(subject) {
                    html += '<tr>';
                    html += '<td>' + escapeHtml(subject.subject_name) + '</td>';
                    html += '<td>' + subject.total_marks + '</td>';
                    html += '<td><input type="number" class="form-control form-control-sm marks-input" ';
                    html += 'name="marks[' + subject.id + ']" min="0" max="' + subject.total_marks + '" required ';
                    html += 'data-total="' + subject.total_marks + '"></td>';
                    html += '<td class="grade-cell">-</td>';
                    html += '</tr>';
                });

                html += '</tbody></table>';
                html += '<div class="row mt-3">';
                html += '<div class="col-md-4"><strong>Total: </strong><span id="totalMarks">0</span></div>';
                html += '<div class="col-md-4"><strong>Percentage: </strong><span id="totalPercentage">0</span>%</div>';
                html += '<div class="col-md-4"><strong>Overall Grade: </strong><span id="overallGrade">-</span></div>';
                html += '</div>';

                container.innerHTML = html;
                attachMarksListeners();
            })
            .catch(err => {
                container.innerHTML = '<p class="text-danger">Error loading subjects.</p>';
                console.error('Subjects load error:', err);
            });
    };

    function attachMarksListeners() {
        document.querySelectorAll('.marks-input').forEach(function(input) {
            input.addEventListener('input', function() {
                const max = parseInt(this.getAttribute('max'));
                let val = parseInt(this.value) || 0;
                if (val > max) {
                    this.value = max;
                    val = max;
                }
                if (val < 0) {
                    this.value = 0;
                    val = 0;
                }

                // Update individual grade
                const total = parseInt(this.getAttribute('data-total'));
                const pct = (val / total) * 100;
                const grade = calculateGrade(pct);
                this.closest('tr').querySelector('.grade-cell').textContent = grade;

                // Recalculate totals
                recalculateTotals();
            });
        });
    }

    function recalculateTotals() {
        let totalObtained = 0;
        let totalMax = 0;

        document.querySelectorAll('.marks-input').forEach(function(input) {
            const val = parseInt(input.value) || 0;
            const max = parseInt(input.getAttribute('data-total')) || 100;
            totalObtained += val;
            totalMax += max;
        });

        const percentage = totalMax > 0 ? ((totalObtained / totalMax) * 100).toFixed(2) : 0;
        const grade = calculateGrade(percentage);

        const totalEl = document.getElementById('totalMarks');
        const pctEl = document.getElementById('totalPercentage');
        const gradeEl = document.getElementById('overallGrade');

        if (totalEl) totalEl.textContent = totalObtained + ' / ' + totalMax;
        if (pctEl) pctEl.textContent = percentage;
        if (gradeEl) {
            gradeEl.textContent = grade;
            gradeEl.className = grade === 'Fail' ? 'text-danger fw-bold' : 'text-success fw-bold';
        }
    }

    function calculateGrade(percentage) {
        percentage = parseFloat(percentage);
        if (percentage >= 75) return 'A';
        if (percentage >= 60) return 'B';
        if (percentage >= 50) return 'C';
        return 'Fail';
    }

    function escapeHtml(text) {
        var div = document.createElement('div');
        div.appendChild(document.createTextNode(text));
        return div.innerHTML;
    }

})();