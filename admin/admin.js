async function fetchSubjects() {
    try {
        const response = await fetch('api/subjects.php');
        if (!response.ok) throw new Error('Ошибка загрузки');
        return await response.json();
    } catch (error) {
        console.error('Ошибка загрузки предметов:', error);
        showNotification('Ошибка загрузки предметов', 'error');
        return [];
    }
}

async function addSubject(name) {
    const response = await fetch('api/subjects.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ name: name })
    });
    if (!response.ok) {
        const error = await response.json();
        throw new Error(error.error || 'Ошибка добавления');
    }
    return await response.json();
}

async function deleteSubject(id) {
    const response = await fetch('api/subjects.php', {
        method: 'DELETE',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ id: id })
    });
    if (!response.ok) {
        const error = await response.json();
        throw new Error(error.error || 'Ошибка удаления');
    }
    return await response.json();
}

async function fetchTeachers() {
    try {
        const response = await fetch('api/teachers.php');
        if (!response.ok) throw new Error('Ошибка загрузки');
        return await response.json();
    } catch (error) {
        console.error('Ошибка загрузки учителей:', error);
        return [];
    }
}

async function addTeacher(data) {
    const response = await fetch('api/teachers.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(data)
    });
    if (!response.ok) {
        const error = await response.json();
        throw new Error(error.error || 'Ошибка добавления');
    }
    return await response.json();
}

async function deleteTeacher(id) {
    const response = await fetch('api/teachers.php', {
        method: 'DELETE',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ id: id })
    });
    if (!response.ok) {
        const error = await response.json();
        throw new Error(error.error || 'Ошибка удаления');
    }
    return await response.json();
}

async function fetchTests() {
    try {
        const response = await fetch('api/tests.php');
        if (!response.ok) throw new Error('Ошибка загрузки');
        return await response.json();
    } catch (error) {
        console.error('Ошибка загрузки тестов:', error);
        return [];
    }
}

async function addTest(testData) {
    const response = await fetch('api/tests.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(testData)
    });
    if (!response.ok) {
        const error = await response.json();
        throw new Error(error.error || 'Ошибка создания');
    }
    return await response.json();
}

async function deleteTest(id) {
    const response = await fetch('api/tests.php', {
        method: 'DELETE',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ id: id })
    });
    if (!response.ok) {
        const error = await response.json();
        throw new Error(error.error || 'Ошибка удаления');
    }
    return await response.json();
}

async function fetchStats() {
    try {
        const response = await fetch('api/stats.php');
        if (!response.ok) throw new Error('Ошибка загрузки');
        return await response.json();
    } catch (error) {
        console.error('Ошибка загрузки статистики:', error);
        return { subjects: 0, teachers: 0, tests: 0 };
    }
}


async function loadSubjectsTable() {
    const tbody = document.getElementById('subjects-table-body');
    if (!tbody) return;
    
    tbody.innerHTML = '<tr><td colspan="5">Загрузка...</td></tr>';
    const subjects = await fetchSubjects();
    
    if (subjects.length === 0) {
        tbody.innerHTML = '<tr><td colspan="5">Нет предметов</td></tr>';
        return;
    }
    
    tbody.innerHTML = '';
    subjects.forEach(subject => {
        const row = tbody.insertRow();
        row.innerHTML = `
          
            <td>${subject.name}</td>
            <td>${subject.teacher_count || 0}</td>
            <td>${subject.test_count || 0}</td>
            <td><button class="btn btn-sm btn-danger delete-subject" data-id="${subject.id}" data-name="${subject.name}">Удалить</button></td>
        `;
    });
}

async function loadTeachersTable() {
    const tbody = document.getElementById('teachers-table-body');
    if (!tbody) return;
    
    tbody.innerHTML = '<tr><td colspan="6">Загрузка...</td></tr>';
    const teachers = await fetchTeachers();
    
    if (teachers.length === 0) {
        tbody.innerHTML = '<tr><td colspan="6">Нет учителей</td></tr>';
        return;
    }
    
    tbody.innerHTML = '';
    teachers.forEach(teacher => {
        const row = tbody.insertRow();
        row.innerHTML = `       
            <td>${teacher.full_name}</td>
            <td>${teacher.login}</td>
            <td>••••••</td>
            <td>${teacher.subject_name || 'Не указан'}</td>
            <td><button class="btn btn-sm btn-danger delete-teacher" data-id="${teacher.id}" data-name="${teacher.full_name}">Удалить</button></td>
        `;
    });
}

async function loadTestsTable() {
    const tbody = document.getElementById('tests-table-body');
    if (!tbody) return;
    
    tbody.innerHTML = '<tr><td colspan="6">Загрузка...</td></tr>';
    const tests = await fetchTests();
    
    if (tests.length === 0) {
        tbody.innerHTML = '<tr><td colspan="6">Нет тестов</td></tr>';
        return;
    }
    
    tbody.innerHTML = '';
    tests.forEach(test => {
        const row = tbody.insertRow();
        row.innerHTML = `
           
            <td>${test.name}</td>
            <td>${test.subject_name || 'Не указан'}</td>
            <td>${test.question_count || 0}</td>
            <td>${test.created_date || test.created_at || ''}</td>
            <td><button class="btn btn-sm btn-danger delete-test" data-id="${test.id}" data-name="${test.name}">Удалить</button></td>
        `;
    });
}

async function updateStats() {
    const stats = await fetchStats();
    const teachersCount = document.getElementById('teachers-count');
    const subjectsCount = document.getElementById('subjects-count');
    const testsCount = document.getElementById('tests-count');
    
    if (teachersCount) teachersCount.textContent = stats.teachers || 0;
    if (subjectsCount) subjectsCount.textContent = stats.subjects || 0;
    if (testsCount) testsCount.textContent = stats.tests || 0;
}


async function populateSubjectDropdowns() {
    const subjects = await fetchSubjects();
    
    const teacherSelect = document.getElementById('teacher-subject');
    if (teacherSelect) {
        teacherSelect.innerHTML = '<option value="">Выберите предмет</option>';
        subjects.forEach(s => {
            teacherSelect.innerHTML += `<option value="${s.id}">${s.name}</option>`;
        });
    }
    
    const testSelect = document.getElementById('test-subject');
    if (testSelect) {
        testSelect.innerHTML = '<option value="">Выберите предмет</option>';
        subjects.forEach(s => {
            testSelect.innerHTML += `<option value="${s.id}">${s.name}</option>`;
        });
    }
}

document.getElementById('subject-form')?.addEventListener('submit', async (e) => {
    e.preventDefault();
    const name = document.getElementById('subject-name').value;
    if (!name.trim()) {
        showNotification('Введите название предмета', 'error');
        return;
    }
    try {
        await addSubject(name);
        showNotification('Предмет добавлен', 'success');
        document.getElementById('add-subject-modal').classList.remove('active');
        document.getElementById('subject-form').reset();
        await loadSubjectsTable();
        await updateStats();
        await populateSubjectDropdowns();
    } catch (err) {
        showNotification(err.message, 'error');
    }
});

document.getElementById('teacher-form')?.addEventListener('submit', async (e) => {
    e.preventDefault();
    const full_name = document.getElementById('teacher-full-name').value;
    const login = document.getElementById('teacher-login').value;
    const password = document.getElementById('teacher-password').value;
    const subject_id = document.getElementById('teacher-subject').value;
    
    if (!full_name || !login || !password) {
        showNotification('Заполните все поля', 'error');
        return;
    }
    try {
        await addTeacher({ full_name, login, password, subject_id: subject_id || null });
        showNotification('Учитель добавлен', 'success');
        document.getElementById('add-teacher-modal').classList.remove('active');
        document.getElementById('teacher-form').reset();
        await loadTeachersTable();
        await updateStats();
    } catch (err) {
        showNotification(err.message, 'error');
    }
});


document.addEventListener('click', async (e) => {
    const btn = e.target.closest('.delete-subject');
    if (btn) {
        const id = btn.dataset.id;
        const name = btn.dataset.name;
        if (confirm(`Удалить предмет "${name}"?`)) {
            try {
                await deleteSubject(id);
                showNotification('Предмет удалён', 'success');
                await loadSubjectsTable();
                await updateStats();
                await populateSubjectDropdowns();
            } catch (err) {
                showNotification(err.message, 'error');
            }
        }
    }
    
    const teacherBtn = e.target.closest('.delete-teacher');
    if (teacherBtn) {
        const id = teacherBtn.dataset.id;
        const name = teacherBtn.dataset.name;
        if (confirm(`Удалить учителя "${name}"?`)) {
            try {
                await deleteTeacher(id);
                showNotification('Учитель удалён', 'success');
                await loadTeachersTable();
                await updateStats();
            } catch (err) {
                showNotification(err.message, 'error');
            }
        }
    }
    
    const testBtn = e.target.closest('.delete-test');
    if (testBtn) {
        const id = testBtn.dataset.id;
        const name = testBtn.dataset.name;
        if (confirm(`Удалить тест "${name}"?`)) {
            try {
                await deleteTest(id);
                showNotification('Тест удалён', 'success');
                await loadTestsTable();
                await updateStats();
            } catch (err) {
                showNotification(err.message, 'error');
            }
        }
    }
});


document.querySelectorAll('.nav-link').forEach(link => {
    link.addEventListener('click', (e) => {
        e.preventDefault();
        const href = link.getAttribute('href');
        if (href && href !== '#') {
            window.location.href = href;
        }
    });
});

function showNotification(message, type) {
    alert(message);
}


document.addEventListener('DOMContentLoaded', async () => {
    const currentPage = window.location.pathname.split('/').pop();
    
    if (currentPage === 'admin.html' || currentPage === '') {
        await updateStats();
        await loadTeachersTable();
        await loadSubjectsTable();
        await loadTestsTable();
        await populateSubjectDropdowns();
        
        const currentDate = document.getElementById('current-date');
        if (currentDate) {
            const now = new Date();
            currentDate.textContent = now.toLocaleDateString('ru-RU', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' });
        }
    } else if (currentPage === 'subjects.html') {
        await loadSubjectsTable();
        await populateSubjectDropdowns();
    } else if (currentPage === 'teachers.html') {
        await loadTeachersTable();
        await populateSubjectDropdowns();
    } else if (currentPage === 'tests.html') {
        await loadTestsTable();
        await populateSubjectDropdowns();
    }
    
    const modals = ['add-subject-modal', 'add-teacher-modal', 'add-test-modal', 'delete-confirm-modal'];
    modals.forEach(modalId => {
        const modal = document.getElementById(modalId);
        if (!modal) return;
        
        const closeBtn = modal.querySelector('.modal-close');
        if (closeBtn) {
            closeBtn.addEventListener('click', () => modal.classList.remove('active'));
        }
    });
    
    document.getElementById('add-subject-btn')?.addEventListener('click', () => {
        document.getElementById('add-subject-modal')?.classList.add('active');
    });
    
    document.getElementById('add-teacher-btn')?.addEventListener('click', () => {
        document.getElementById('add-teacher-modal')?.classList.add('active');
    });
    
    document.getElementById('add-test-btn')?.addEventListener('click', () => {
        document.getElementById('add-test-modal')?.classList.add('active');
    });
});


let questionCounter = 1;
function collectTestQuestions() {
    const questions = [];
    const questionItems = document.querySelectorAll('.question-item');
    
    questionItems.forEach(item => {
        const questionId = item.getAttribute('data-question-id');
        const questionText = document.getElementById(`question-text-${questionId}`)?.value.trim();
        
        if (!questionText) return;
        
        const options = [];
        const optionItems = item.querySelectorAll('.option-item');
        
        optionItems.forEach(optEl => {
            const checkbox = optEl.querySelector('.correct-checkbox');
            const textInput = optEl.querySelector('.option-text');
            const optionText = textInput?.value.trim();
            
            if (optionText) {
                options.push({
                    text: optionText,
                    isCorrect: checkbox ? checkbox.checked : false
                });
            }
        });
        
        if (questionText && options.length >= 2) {
            questions.push({
                text: questionText,
                options: options
            });
        }
    });
    
    return questions;
}

function validateTestQuestions(questions) {
    for (let i = 0; i < questions.length; i++) {
        const q = questions[i];
        if (!q.text || q.text.length < 3) {
            return { valid: false, message: `Вопрос ${i+1}: текст слишком короткий` };
        }
        if (q.options.length < 2) {
            return { valid: false, message: `Вопрос ${i+1}: нужно минимум 2 варианта ответа` };
        }
        
        let hasCorrect = false;
        for (let opt of q.options) {
            if (opt.isCorrect) hasCorrect = true;
        }
        if (!hasCorrect) {
            return { valid: false, message: `Вопрос ${i+1}: отметьте правильный ответ` };
        }
    }
    return { valid: true };
}

function addQuestion() {
    questionCounter++;
    
    const newQuestion = document.createElement('div');
    newQuestion.className = 'question-item';
    newQuestion.setAttribute('data-question-id', questionCounter);
    newQuestion.innerHTML = `
        <div class="question-header">
            <div class="question-text">Вопрос #${questionCounter}</div>
            <button type="button" class="btn btn-danger btn-sm remove-question-btn">
                <i class="fas fa-trash"></i> Удалить
            </button>
        </div>
        <div class="form-group">
            <label for="question-text-${questionCounter}">Текст вопроса *</label>
            <input type="text" id="question-text-${questionCounter}" placeholder="Введите вопрос" required>
        </div>
        <div class="options-list">
            <div class="option-item">
                <input type="checkbox" class="correct-checkbox" name="correct-answer-${questionCounter}" value="1">
                <input type="text" placeholder="Вариант ответа 1" class="option-text" required>
            </div>
            <div class="option-item">
                <input type="checkbox" class="correct-checkbox" name="correct-answer-${questionCounter}" value="2">
                <input type="text" placeholder="Вариант ответа 2" class="option-text" required>
            </div>
        </div>
        <button type="button" class="btn btn-sm add-option-btn" data-question-id="${questionCounter}">
            <i class="fas fa-plus"></i> Добавить вариант ответа
        </button>
    `;
    
    document.getElementById('questions-container').appendChild(newQuestion);
    updateQuestionNumbers();
}

function addOption(questionId) {
    const questionDiv = document.querySelector(`.question-item[data-question-id="${questionId}"]`);
    if (!questionDiv) return;
    
    const optionsList = questionDiv.querySelector('.options-list');
    const currentCount = optionsList.querySelectorAll('.option-item').length;
    const newNumber = currentCount + 1;
    
    const newOption = document.createElement('div');
    newOption.className = 'option-item';
    newOption.innerHTML = `
        <input type="checkbox" class="correct-checkbox" name="correct-answer-${questionId}" value="${newNumber}">
        <input type="text" placeholder="Вариант ответа ${newNumber}" class="option-text" required>
    `;
    
    optionsList.appendChild(newOption);
}

function updateQuestionNumbers() {
    const questions = document.querySelectorAll('.question-item');
    questions.forEach((q, idx) => {
        const newNum = idx + 1;
        q.setAttribute('data-question-id', newNum);
        
        const titleSpan = q.querySelector('.question-text');
        if (titleSpan) titleSpan.textContent = `Вопрос #${newNum}`;
        
        const textInput = q.querySelector('input[type="text"]');
        if (textInput && textInput.id) {
            textInput.id = `question-text-${newNum}`;
        }
        
        const checkboxes = q.querySelectorAll('input[type="checkbox"]');
        checkboxes.forEach((checkbox, rIdx) => {
            checkbox.name = `correct-answer-${newNum}`;
            checkbox.value = rIdx + 1;
        });
        
        const addBtn = q.querySelector('.add-option-btn');
        if (addBtn) {
            addBtn.setAttribute('data-question-id', newNum);
        }
    });
    questionCounter = questions.length;
}
function resetTestForm() {
    const container = document.getElementById('questions-container');
    if (!container) return;
    
    container.innerHTML = '<h4>Вопросы теста</h4>';
    questionCounter = 0;
    addQuestion();
    
    const nameInput = document.getElementById('test-name');
    if (nameInput) nameInput.value = '';
    
    const subjectSelect = document.getElementById('test-subject');
    if (subjectSelect) subjectSelect.value = '';
}


document.getElementById('test-form')?.addEventListener('submit', async (e) => {
    e.preventDefault();
    
    const testName = document.getElementById('test-name')?.value.trim();
    const subjectId = document.getElementById('test-subject')?.value;
    
    if (!testName) {
        showNotification('Введите название теста', 'error');
        return;
    }
    if (!subjectId) {
        showNotification('Выберите предмет', 'error');
        return;
    }
    
    const questions = collectTestQuestions();
    if (questions.length === 0) {
        showNotification('Добавьте хотя бы один вопрос', 'error');
        return;
    }
    
    const validation = validateTestQuestions(questions);
    if (!validation.valid) {
        showNotification(validation.message, 'error');
        return;
    }
    
    try {
        await addTest({
            name: testName,
            subject_id: parseInt(subjectId),
            questions: questions
        });
        
        showNotification('Тест успешно создан', 'success');
        document.getElementById('add-test-modal').classList.remove('active');
        resetTestForm();
        await loadTestsTable();
        await updateStats();
        
    } catch (err) {
        showNotification(err.message, 'error');
    }
});

document.getElementById('add-question-btn')?.addEventListener('click', () => {
    addQuestion();
});

document.addEventListener('click', (e) => {
    if (e.target.closest('.remove-question-btn')) {
        const questionItem = e.target.closest('.question-item');
        const allQuestions = document.querySelectorAll('.question-item');
        if (allQuestions.length > 1) {
            questionItem.remove();
            updateQuestionNumbers();
        } else {
            showNotification('Тест должен содержать хотя бы один вопрос', 'error');
        }
    }
    
    if (e.target.closest('.add-option-btn')) {
        const btn = e.target.closest('.add-option-btn');
        const questionId = btn.getAttribute('data-question-id');
        addOption(questionId);
    }
});

document.getElementById('cancel-test-btn')?.addEventListener('click', () => {
    document.getElementById('add-test-modal').classList.remove('active');
    resetTestForm();
});

document.getElementById('close-test-modal')?.addEventListener('click', () => {
    document.getElementById('add-test-modal').classList.remove('active');
    resetTestForm();
});

const addTestBtn = document.getElementById('add-test-btn');
if (addTestBtn) {
    addTestBtn.addEventListener('click', () => {
        resetTestForm();
        populateSubjectDropdowns();
    });
}