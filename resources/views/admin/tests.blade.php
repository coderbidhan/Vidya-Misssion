@extends('layouts.admin')
@section('title','Tests & Questions')
@section('page-title','Tests & Questions')
@section('page-subtitle','Create chapter tests and manage MCQ question banks')
@section('topbar-actions')
    <button class="btn btn-primary btn-sm" onclick="openModal('addTestModal')">＋ Create Test</button>
@endsection

@section('content')
<div class="tests-layout">
    <!-- Tests List Panel -->
    <div class="card" style="height:fit-content">
        <div class="card-header" style="padding:0.85rem 1rem">
            <span class="card-title" style="font-size:0.88rem">🧪 Active Tests</span>
            <span id="testCountBadge" class="badge badge-purple">0</span>
        </div>
        <div id="testList" style="padding:0.5rem;min-height:220px;max-height:calc(100vh - 12rem);overflow-y:auto">
            <div style="text-align:center;padding:2rem;color:var(--text-muted)"><div class="spinner-sm"></div></div>
        </div>
    </div>

    <!-- Questions Bank Panel -->
    <div class="card">
        <div class="card-header" style="padding:0.85rem 1.25rem;flex-wrap:wrap;gap:0.75rem">
            <div>
                <span class="card-title" id="questionPanelTitle">📝 Select a Test</span>
                <div id="testMetaSub" style="font-size:0.75rem;color:var(--text-muted);margin-top:0.2rem"></div>
            </div>
            <div id="questionActions" style="display:none;gap:0.4rem;align-items:center">
                <button class="btn btn-outline btn-sm" onclick="openModal('bulkModal')">📤 Bulk JSON</button>
                <button class="btn btn-primary btn-sm" onclick="openAddQuestionModal()">＋ Add Question</button>
            </div>
        </div>
        <div id="questionList" style="padding:1rem;min-height:300px">
            <div class="empty-state">
                <div class="empty-icon">🧪</div>
                <p>Select a test from the left panel to manage its questions.</p>
            </div>
        </div>
    </div>
</div>

<!-- Add Test Modal -->
<div class="modal-backdrop" id="addTestModal">
    <div class="modal" style="max-width:520px">
        <div class="modal-header">
            <span class="modal-title">➕ Create Chapter Test</span>
            <button class="modal-close" onclick="closeModal('addTestModal')">×</button>
        </div>
        <div class="modal-body">
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Class Level</label>
                    <select class="form-control" id="at-class" onchange="cascadeTestModal('class')">
                        <option value="">Select class…</option>
                        @for($c=1;$c<=12;$c++)
                            <option value="{{ $c }}">Class {{ $c }}</option>
                        @endfor
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Subject</label>
                    <select class="form-control bn-font" id="at-subject" onchange="cascadeTestModal('subject')" disabled>
                        <option value="">Select subject…</option>
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Chapter *</label>
                <select class="form-control bn-font" id="at-chapter" disabled>
                    <option value="">Select chapter…</option>
                </select>
            </div>

            <div class="form-group">
                <label class="form-label">Test Title (Bengali / English) *</label>
                <input type="text" class="form-control bn-font" id="at-title" placeholder="e.g. Chapter 1 Practice MCQ Test" required>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Pass Marks *</label>
                    <input type="number" class="form-control" id="at-pass" value="10" min="1">
                </div>
                <div class="form-group">
                    <label class="form-label">Duration (Minutes) *</label>
                    <input type="number" class="form-control" id="at-duration" value="20" min="1">
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button class="btn btn-outline" onclick="closeModal('addTestModal')">Cancel</button>
            <button class="btn btn-primary" onclick="createTest()">Create Test</button>
        </div>
    </div>
</div>

<!-- Add Single Question Modal -->
<div class="modal-backdrop" id="addQModal">
    <div class="modal" style="max-width:580px">
        <div class="modal-header">
            <span class="modal-title" id="qModalTitle">➕ Add MCQ Question</span>
            <button class="modal-close" onclick="closeModal('addQModal')">×</button>
        </div>
        <div class="modal-body">
            <input type="hidden" id="q-id">
            <div class="form-group">
                <label class="form-label">Question Text (Dual Bengali / English) *</label>
                <textarea class="form-control bn-font" id="q-text" rows="2" placeholder="e.g. আলোর প্রতিসরণের দ্বিতীয় সূত্রটি কার সূত্র নামে পরিচিত?" required></textarea>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Option A *</label>
                    <input type="text" class="form-control bn-font" id="q-a" placeholder="Option A text" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Option B *</label>
                    <input type="text" class="form-control bn-font" id="q-b" placeholder="Option B text" required>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Option C *</label>
                    <input type="text" class="form-control bn-font" id="q-c" placeholder="Option C text" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Option D *</label>
                    <input type="text" class="form-control bn-font" id="q-d" placeholder="Option D text" required>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Correct Answer Option *</label>
                    <select class="form-control" id="q-correct">
                        <option value="a">Option A</option>
                        <option value="b">Option B</option>
                        <option value="c">Option C</option>
                        <option value="d">Option D</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Marks *</label>
                    <input type="number" class="form-control" id="q-marks" value="1" min="1">
                </div>
            </div>
            <div class="form-group">
                <label class="form-label">Explanation / Solution Note (Optional)</label>
                <input type="text" class="form-control bn-font" id="q-exp" placeholder="Explanation shown to students after test completion">
            </div>
        </div>
        <div class="modal-footer">
            <button class="btn btn-outline" onclick="closeModal('addQModal')">Cancel</button>
            <button class="btn btn-primary" onclick="saveQuestion()">Save Question</button>
        </div>
    </div>
</div>

<!-- Bulk Question Upload Modal -->
<div class="modal-backdrop" id="bulkModal">
    <div class="modal" style="max-width:620px">
        <div class="modal-header">
            <span class="modal-title">📤 Bulk Upload Questions (JSON)</span>
            <button class="modal-close" onclick="closeModal('bulkModal')">×</button>
        </div>
        <div class="modal-body">
            <p style="font-size:0.8rem;color:var(--text-muted);margin-bottom:0.75rem">Paste a JSON array of question objects:</p>
            <textarea class="form-control bn-font" id="bulkJson" rows="9" style="font-family:monospace;font-size:0.78rem" placeholder='[
  {
    "question": "What is the speed of light in vacuum?",
    "option_a": "3 x 10^8 m/s",
    "option_b": "3 x 10^6 m/s",
    "option_c": "3 x 10^5 km/s",
    "option_d": "3 x 10^3 m/s",
    "correct_option": "a",
    "marks": 1
  }
]'></textarea>
        </div>
        <div class="modal-footer">
            <button class="btn btn-outline" onclick="closeModal('bulkModal')">Cancel</button>
            <button class="btn btn-primary" onclick="bulkUpload()">Upload Questions</button>
        </div>
    </div>
</div>

<style>
.tests-layout {
    display: grid;
    grid-template-columns: 320px 1fr;
    gap: 1.25rem;
}

@media (max-width: 860px) {
    .tests-layout { grid-template-columns: 1fr; }
}

.test-item-card {
    padding: 0.75rem 0.85rem;
    border-radius: 12px;
    border: 1.5px solid transparent;
    cursor: pointer;
    transition: all 0.18s;
    background: #fff;
    margin-bottom: 0.4rem;
}
.test-item-card:hover { background: rgba(91,69,255,0.04); border-color: rgba(91,69,255,0.15); }
.test-item-card.active { background: rgba(91,69,255,0.08); border-color: rgba(91,69,255,0.35); }

.q-card {
    padding: 1rem;
    border-radius: 12px;
    background: var(--bg-app);
    border: 1px solid var(--border-light);
    margin-bottom: 0.75rem;
    position: relative;
}
.q-opt {
    padding: 0.4rem 0.6rem;
    border-radius: 8px;
    background: #fff;
    border: 1px solid var(--border-light);
    font-size: 0.82rem;
    margin-top: 0.35rem;
    display: flex;
    align-items: center;
    gap: 0.4rem;
}
.q-opt.correct {
    background: rgba(16,185,129,0.1);
    border-color: rgba(16,185,129,0.4);
    color: #065F46;
    font-weight: 700;
}
</style>
@endsection

@section('scripts')
<script>
let selectedTestId = null;
let allTests       = [];

async function loadTests() {
    try {
        const res = await api('GET', '/admin/tests');
        allTests = res.data || [];
        document.getElementById('testCountBadge').textContent = allTests.length;

        if (!allTests.length) {
            document.getElementById('testList').innerHTML = '<div class="empty-state" style="padding:2rem"><p>No tests created yet.</p></div>';
            return;
        }

        document.getElementById('testList').innerHTML = allTests.map(t => `
            <div class="test-item-card ${t.id === selectedTestId ? 'active' : ''}" id="tc-${t.id}" onclick="selectTest(${t.id})">
                <div style="display:flex;justify-content:space-between;align-items:flex-start;gap:0.5rem">
                    <div style="font-weight:700;font-size:0.86rem;line-height:1.3" class="bn-font">${t.title}</div>
                    <button class="btn btn-danger btn-sm" style="padding:0.15rem 0.35rem;font-size:0.65rem" onclick="event.stopPropagation();deleteTest(${t.id})" title="Delete Test">✕</button>
                </div>
                <div style="font-size:0.73rem;color:var(--text-muted);margin-top:0.35rem">
                    ${t.chapter?.title ? `<span class="bn-font">📖 ${t.chapter.title}</span> • ` : ''}
                    ⏱ ${t.duration_minutes}m • 🎯 Pass: ${t.pass_marks}
                </div>
            </div>
        `).join('');

        if (selectedTestId) selectTest(selectedTestId);
        else if (allTests.length > 0) selectTest(allTests[0].id);

    } catch(e) { toast('Error loading tests: ' + e.message, 'error'); }
}

async function selectTest(testId) {
    selectedTestId = testId;
    document.querySelectorAll('.test-item-card').forEach(el => el.classList.remove('active'));
    const activeEl = document.getElementById('tc-' + testId);
    if (activeEl) activeEl.classList.add('active');

    const test = allTests.find(t => t.id === testId);
    if (!test) return;

    document.getElementById('questionPanelTitle').innerHTML = `📝 <span class="bn-font">${test.title}</span>`;
    document.getElementById('testMetaSub').textContent = `Total Marks: ${test.total_marks || 0} • Pass Marks: ${test.pass_marks} • Duration: ${test.duration_minutes} mins`;
    document.getElementById('questionActions').style.display = 'flex';

    document.getElementById('questionList').innerHTML = '<div style="text-align:center;padding:2rem"><div class="spinner-sm"></div> Loading question bank…</div>';

    try {
        const res = await api('GET', `/admin/tests/${testId}/questions`);
        const questions = res.data?.questions || res.data || [];
        renderQuestions(questions);
    } catch(e) {
        toast('Error loading questions: ' + e.message, 'error');
    }
}

function renderQuestions(questions) {
    if (!questions.length) {
        document.getElementById('questionList').innerHTML =
            '<div class="empty-state"><div class="empty-icon">📝</div><p>No questions added to this test yet.<br>Click <strong>+ Add Question</strong> or <strong>Bulk JSON</strong>.</p></div>';
        return;
    }

    document.getElementById('questionList').innerHTML = questions.map((q, idx) => {
        const correct = (q.correct_option || '').toLowerCase();
        return `
            <div class="q-card">
                <div style="display:flex;justify-content:space-between;align-items:flex-start;gap:0.75rem">
                    <div style="font-weight:700;font-size:0.88rem;color:var(--text)" class="bn-font">
                        <span style="color:var(--primary);margin-right:0.25rem">Q${idx + 1}.</span> ${q.question}
                    </div>
                    <div style="display:flex;gap:0.3rem;flex-shrink:0">
                        <span class="badge badge-purple" style="font-size:0.7rem">${q.marks || 1} Mark</span>
                        <button class="btn btn-outline btn-sm" style="padding:0.2rem 0.45rem;font-size:0.7rem" onclick="editQuestion(JSON.parse(this.dataset.question))" data-question='${JSON.stringify(q).replace(/'/g, "&#39;")}'>✏️</button>
                        <button class="btn btn-danger btn-sm" style="padding:0.2rem 0.45rem;font-size:0.7rem" onclick="deleteQuestion(${q.id})">🗑</button>
                    </div>
                </div>

                <div style="display:grid;grid-template-columns:1fr 1fr;gap:0.5rem;margin-top:0.6rem">
                    <div class="q-opt ${correct === 'a' ? 'correct' : ''}">
                        <strong>A:</strong> <span class="bn-font">${q.option_a}</span> ${correct === 'a' ? '✓' : ''}
                    </div>
                    <div class="q-opt ${correct === 'b' ? 'correct' : ''}">
                        <strong>B:</strong> <span class="bn-font">${q.option_b}</span> ${correct === 'b' ? '✓' : ''}
                    </div>
                    <div class="q-opt ${correct === 'c' ? 'correct' : ''}">
                        <strong>C:</strong> <span class="bn-font">${q.option_c}</span> ${correct === 'c' ? '✓' : ''}
                    </div>
                    <div class="q-opt ${correct === 'd' ? 'correct' : ''}">
                        <strong>D:</strong> <span class="bn-font">${q.option_d}</span> ${correct === 'd' ? '✓' : ''}
                    </div>
                </div>

                ${q.explanation ? `<div style="font-size:0.75rem;color:var(--text-muted);margin-top:0.5rem;background:#fff;padding:0.35rem 0.65rem;border-radius:6px;border-left:3px solid var(--primary)">💡 ${q.explanation}</div>` : ''}
            </div>
        `;
    }).join('');
}

async function cascadeTestModal(level) {
    const cls = document.getElementById('at-class').value;
    const sub = document.getElementById('at-subject');
    const chp = document.getElementById('at-chapter');

    if (level === 'class') {
        if (!cls) { sub.disabled = true; chp.disabled = true; return; }
        sub.innerHTML = '<option value="">Loading…</option>';
        sub.disabled = true;
        try {
            const res = await api('GET', `/admin/class-subjects?class_level=${cls}`);
            const subjects = res.data?.assigned_subjects || [];
            sub.innerHTML = '<option value="">Select subject…</option>' + 
                subjects.map(s => `<option value="${s.id}">${s.name}</option>`).join('');
            sub.disabled = false;
        } catch(e) { toast(e.message, 'error'); }
    } else if (level === 'subject') {
        if (!sub.value) { chp.disabled = true; return; }
        chp.innerHTML = '<option value="">Loading…</option>';
        chp.disabled = true;
        try {
            const res = await api('GET', `/admin/class-subjects/${cls}/chapters?subject_id=${sub.value}`);
            const chapters = res.data || [];
            chp.innerHTML = '<option value="">Select chapter…</option>' + 
                chapters.map(c => `<option value="${c.id}">${c.title}</option>`).join('');
            chp.disabled = false;
        } catch(e) { toast(e.message, 'error'); }
    }
}

async function createTest() {
    const chapter_id = parseInt(document.getElementById('at-chapter').value);
    const title      = document.getElementById('at-title').value.trim();
    const pass_marks = parseInt(document.getElementById('at-pass').value) || 10;
    const duration   = parseInt(document.getElementById('at-duration').value) || 20;

    if (!chapter_id || !title) {
        toast('Please select a chapter and enter a test title.', 'error');
        return;
    }

    try {
        const res = await api('POST', '/admin/tests', {
            chapter_id,
            title,
            pass_marks,
            duration_minutes: duration,
        });
        toast('Test created successfully!', 'success');
        closeModal('addTestModal');
        selectedTestId = res.data?.id || null;
        await loadTests();
    } catch(e) { toast(e.message, 'error'); }
}

async function deleteTest(id) {
    if (!confirm('Delete this test and all its questions?')) return;
    try {
        await api('DELETE', `/admin/tests/${id}`);
        toast('Test deleted.', 'success');
        if (selectedTestId === id) selectedTestId = null;
        await loadTests();
    } catch(e) { toast(e.message, 'error'); }
}

function openAddQuestionModal() {
    document.getElementById('q-id').value = '';
    document.getElementById('q-text').value = '';
    document.getElementById('q-a').value = '';
    document.getElementById('q-b').value = '';
    document.getElementById('q-c').value = '';
    document.getElementById('q-d').value = '';
    document.getElementById('q-correct').value = 'a';
    document.getElementById('q-marks').value = '1';
    document.getElementById('q-exp').value = '';
    document.getElementById('qModalTitle').textContent = '➕ Add MCQ Question';
    openModal('addQModal');
}

function editQuestion(q) {
    document.getElementById('q-id').value = q.id;
    document.getElementById('q-text').value = q.question;
    document.getElementById('q-a').value = q.option_a;
    document.getElementById('q-b').value = q.option_b;
    document.getElementById('q-c').value = q.option_c;
    document.getElementById('q-d').value = q.option_d;
    document.getElementById('q-correct').value = (q.correct_option || 'a').toLowerCase();
    document.getElementById('q-marks').value = q.marks || 1;
    document.getElementById('q-exp').value = q.explanation || '';
    document.getElementById('qModalTitle').textContent = '✏️ Edit MCQ Question';
    openModal('addQModal');
}

async function saveQuestion() {
    if (!selectedTestId) return;

    const id = document.getElementById('q-id').value;
    const body = {
        question:       document.getElementById('q-text').value.trim(),
        option_a:       document.getElementById('q-a').value.trim(),
        option_b:       document.getElementById('q-b').value.trim(),
        option_c:       document.getElementById('q-c').value.trim(),
        option_d:       document.getElementById('q-d').value.trim(),
        correct_option: document.getElementById('q-correct').value,
        marks:          parseInt(document.getElementById('q-marks').value) || 1,
        explanation:    document.getElementById('q-exp').value.trim() || null,
    };

    if (!body.question || !body.option_a || !body.option_b || !body.option_c || !body.option_d) {
        toast('Please fill question text and all 4 options.', 'error');
        return;
    }

    try {
        if (id) {
            await api('PUT', `/admin/questions/${id}`, body);
            toast('Question updated!', 'success');
        } else {
            await api('POST', `/admin/tests/${selectedTestId}/questions/single`, body);
            toast('Question added!', 'success');
        }
        closeModal('addQModal');
        await selectTest(selectedTestId);
    } catch(e) { toast(e.message, 'error'); }
}

async function deleteQuestion(id) {
    if (!confirm('Delete this question?')) return;
    try {
        await api('DELETE', `/admin/questions/${id}`);
        toast('Question deleted.', 'success');
        await selectTest(selectedTestId);
    } catch(e) { toast(e.message, 'error'); }
}

async function bulkUpload() {
    if (!selectedTestId) return;
    const raw = document.getElementById('bulkJson').value.trim();
    if (!raw) { toast('Please paste a JSON array of questions.', 'error'); return; }

    try {
        const questions = JSON.parse(raw);
        if (!Array.isArray(questions)) throw new Error('Expected JSON array [ {...}, {...} ]');

        await api('POST', `/admin/tests/${selectedTestId}/questions`, { questions });
        toast(`Uploaded ${questions.length} questions successfully!`, 'success');
        closeModal('bulkModal');
        document.getElementById('bulkJson').value = '';
        await selectTest(selectedTestId);
    } catch(e) { toast('Upload failed: ' + e.message, 'error'); }
}

window._pageInit = loadTests;
</script>
@endsection
