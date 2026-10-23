// ==========================================
// FILE JAVASCRIPT GLOBAL UNTUK SISTEM RESTO
// ==========================================

// 1. FITUR TOGGLE TERSEDIA / HABIS
function toggleAvailable(id, btn) {
    let xhr = new XMLHttpRequest();
    xhr.open("PATCH", "/resto/menu/" + id + "/toggle", true);
    xhr.setRequestHeader("X-CSRF-TOKEN", document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
    xhr.setRequestHeader("Accept", "application/json");
    
    xhr.onreadystatechange = function() {
        if (this.readyState === 4) {
            if (this.status === 200) {
                let data = JSON.parse(this.responseText);
                if (!data.success) return;
                
                const visual = btn.querySelector('.toggle-visual');
                const dot = btn.querySelector('.toggle-dot');
                const text = btn.querySelector('.toggle-text');
                
                if (data.is_available) {
                    visual.classList.replace('bg-gray-300', 'bg-teal-500'); 
                    dot.classList.add('translate-x-4');
                    text.classList.replace('text-gray-400', 'text-teal-600'); 
                    text.textContent = 'Tersedia';
                } else {
                    visual.classList.replace('bg-teal-500', 'bg-gray-300'); 
                    dot.classList.remove('translate-x-4');
                    text.classList.replace('text-teal-600', 'text-gray-400'); 
                    text.textContent = 'Habis';
                }
            } else {
                alert("Gagal mengubah status. Error code: " + this.status);
            }
        }
    };
    xhr.send();
}

// 2. FITUR HAPUS MENU
let pendingDeleteId = null;
let pendingDeleteBtn = null;

function openDeleteModal(id, name, btn) {
    pendingDeleteId = id; 
    pendingDeleteBtn = btn;
    document.getElementById('delete-name').textContent = '"' + name + '"';
    const modal = document.getElementById('delete-modal');
    if (modal) { modal.classList.remove('hidden'); modal.classList.add('flex'); }
}

function closeDeleteModal() {
    const modal = document.getElementById('delete-modal');
    if (modal) { modal.classList.add('hidden'); modal.classList.remove('flex'); }
    pendingDeleteId = null; 
    pendingDeleteBtn = null;
}

function confirmDelete() {
    if (!pendingDeleteId) return;
    
    let xhr = new XMLHttpRequest();
    xhr.open("DELETE", "/resto/menu/" + pendingDeleteId, true);
    xhr.setRequestHeader("X-CSRF-TOKEN", document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
    xhr.setRequestHeader("Accept", "application/json");
    
    xhr.onreadystatechange = function() {
        if (this.readyState === 4 && this.status === 200) {
            let data = JSON.parse(this.responseText);
            if (data.success) {
                const card = pendingDeleteBtn.closest('.bg-white');
                card.style.transition = 'all 0.3s'; 
                card.style.opacity = '0'; 
                card.style.transform = 'scale(0.9)';
                setTimeout(() => card.remove(), 300); 
                closeDeleteModal();
            } else { 
                alert(data.message || 'Gagal hapus'); 
                closeDeleteModal(); 
            }
        }
    };
    xhr.send();
}

// 3. FITUR CETAK QR MEJA
let pendingDeleteTableId = null;

function openDeleteTableModal(id, name) {
    pendingDeleteTableId = id;
    document.getElementById('delete-table-name').textContent = '"' + name + '"';
    const modal = document.getElementById('delete-table-modal');
    if (modal) { modal.classList.remove('hidden'); modal.classList.add('flex'); }
}

function closeDeleteTableModal() {
    const modal = document.getElementById('delete-table-modal');
    if (modal) { modal.classList.add('hidden'); modal.classList.remove('flex'); }
    pendingDeleteTableId = null;
}

function confirmDeleteTable() {
    if (!pendingDeleteTableId) return;
    
    let xhr = new XMLHttpRequest();
    xhr.open("DELETE", "/resto/table/" + pendingDeleteTableId, true);
    xhr.setRequestHeader("X-CSRF-TOKEN", document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
    xhr.setRequestHeader("Accept", "application/json");
    
    xhr.onreadystatechange = function() {
        if (this.readyState === 4) {
            if (this.status === 200) {
                location.reload();
            } else {
                alert("Gagal menghapus meja.");
                closeDeleteTableModal();
            }
        }
    };
    xhr.send();
}

function downloadPng(tableId, tableName) {
    const wrapper = document.getElementById('qr-wrapper-' + tableId);
    const svg = wrapper.querySelector('svg');
    if (!svg) return alert('QR tidak ditemukan');

    const serializer = new XMLSerializer();
    const svgString = serializer.serializeToString(svg);
    const svgBlob = new Blob([svgString], { type: 'image/svg+xml;charset=utf-8' });
    const url = URL.createObjectURL(svgBlob);

    const img = new Image();
    img.onload = function() {
        const canvas = document.createElement('canvas');
        const size = 1000;
        canvas.width = size;
        canvas.height = size + 200;
        const ctx = canvas.getContext('2d');
        ctx.fillStyle = '#ffffff';
        ctx.fillRect(0, 0, canvas.width, canvas.height);
        const qrSize = 800;
        const qrX = (canvas.width - qrSize) / 2;
        const qrY = 50;
        ctx.drawImage(img, qrX, qrY, qrSize, qrSize);
        ctx.fillStyle = '#1f2937';
        ctx.font = 'bold 60px Arial';
        ctx.textAlign = 'center';
        ctx.fillText(tableName, canvas.width / 2, qrY + qrSize + 90);
        ctx.fillStyle = '#14b8a6';
        ctx.font = 'bold 32px Arial';
        ctx.fillText('Scan untuk Pesan Menu', canvas.width / 2, qrY + qrSize + 140);

        const a = document.createElement('a');
        a.download = 'QR-' + tableName.replace(/\s+/g, '-') + '.png';
        a.href = canvas.toDataURL('image/png');
        a.click();
        URL.revokeObjectURL(url);
    };
    img.src = url;
}

// 4. FITUR KATEGORI
function openCategoryModal() {
    const modal = document.getElementById('category-modal');
    if (!modal) return;
    modal.classList.remove('hidden');
    modal.classList.add('flex');
    document.getElementById('category-name').value = '';
    document.getElementById('category-description').value = '';
    document.getElementById('category-error').classList.add('hidden');
    setTimeout(() => document.getElementById('category-name').focus(), 100);
}

function closeCategoryModal() {
    const modal = document.getElementById('category-modal');
    if (modal) { modal.classList.add('hidden'); modal.classList.remove('flex'); }
}

function submitCategory(e) {
    e.preventDefault();
    const name = document.getElementById('category-name').value.trim();
    const description = document.getElementById('category-description').value.trim();
    const errorEl = document.getElementById('category-error');
    if (!name) return;

    let xhr = new XMLHttpRequest();
    xhr.open("POST", "/resto/categories", true);
    xhr.setRequestHeader("Content-Type", "application/json");
    xhr.setRequestHeader("X-CSRF-TOKEN", document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
    xhr.setRequestHeader("Accept", "application/json");

    xhr.onreadystatechange = function() {
        if (this.readyState === 4 && this.status === 200) {
            let data = JSON.parse(this.responseText);
            if (data.success) {
                const select = document.getElementById('category-select');
                const option = document.createElement('option');
                option.value = data.category.id;
                option.textContent = data.category.name;
                option.dataset.description = data.category.description || '';
                option.selected = true;
                select.appendChild(option);
                closeCategoryModal();
            } else {
                errorEl.textContent = data.message || 'Gagal menyimpan kategori.';
                errorEl.classList.remove('hidden');
            }
        }
    };
    xhr.send(JSON.stringify({ name: name, description: description }));
}

function openDeleteCategoryModal() {
    const select = document.getElementById('category-select');
    const listEl = document.getElementById('delete-category-list');
    if (!select || !listEl) return;
    
    listEl.innerHTML = '';
    const options = Array.from(select.options).filter(opt => opt.value !== '');

    if (options.length === 0) {
        listEl.innerHTML = '<p class="text-sm text-gray-400 text-center py-6">Belum ada kategori.</p>';
    } else {
        options.forEach(opt => {
            const item = document.createElement('div');
            item.className = 'flex items-center justify-between bg-gray-50 rounded-xl px-4 py-3';
            item.innerHTML = `
                <span class="text-sm font-medium flex-1">${opt.textContent}</span>
                <div class="flex gap-1">
                    <button type="button" onclick="openEditCategoryModal(${opt.value})" class="w-8 h-8 rounded-lg hover:bg-teal-100 text-teal-600 flex items-center justify-center transition text-sm">✏️</button>
                    <button type="button" onclick="deleteCategory(${opt.value}, this)" class="w-8 h-8 rounded-lg hover:bg-red-100 text-red-500 flex items-center justify-center transition text-sm">🗑️</button>
                </div>
            `;
            listEl.appendChild(item);
        });
    }

    const modal = document.getElementById('delete-category-modal');
    if (modal) { modal.classList.remove('hidden'); modal.classList.add('flex'); }
}

function closeDeleteCategoryModal() {
    const modal = document.getElementById('delete-category-modal');
    if (modal) { modal.classList.add('hidden'); modal.classList.remove('flex'); }
}

function deleteCategory(id, btn) {
    if (!confirm('Yakin ingin menghapus kategori ini?')) return;

    let xhr = new XMLHttpRequest();
    xhr.open("DELETE", "/resto/categories/" + id, true);
    xhr.setRequestHeader("X-CSRF-TOKEN", document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
    xhr.setRequestHeader("Accept", "application/json");

    xhr.onreadystatechange = function() {
        if (this.readyState === 4) {
            if (this.status === 200) {
                let data = JSON.parse(this.responseText);
                if (data.success) {
                    const select = document.getElementById('category-select');
                    const option = select.querySelector('option[value="' + id + '"]');
                    if (option) option.remove();
                    
                    closeDeleteCategoryModal();
                    location.reload();
                } else {
                    alert(data.message || 'Gagal menghapus kategori.');
                }
            } else {
                alert("Terjadi kesalahan pada server. (Error: " + this.status + ")");
            }
        }
    };
    xhr.send();
}

function openEditCategoryModal(categoryId) {
    const select = document.getElementById('category-select');
    const option = select.querySelector('option[value="' + categoryId + '"]');
    if (!option) return;
    closeDeleteCategoryModal(); 

    document.getElementById('edit-category-id').value = categoryId;
    document.getElementById('edit-category-name').value = option.textContent;
    document.getElementById('edit-category-description').value = option.dataset.description || '';
    document.getElementById('edit-category-error').classList.add('hidden');

    const modal = document.getElementById('edit-category-modal');
    if (modal) { modal.classList.remove('hidden'); modal.classList.add('flex'); }
}

function closeEditCategoryModal() {
    const modal = document.getElementById('edit-category-modal');
    if (modal) { modal.classList.add('hidden'); modal.classList.remove('flex'); }
}

function submitEditCategory(e) {
    e.preventDefault();
    const id = document.getElementById('edit-category-id').value;
    const name = document.getElementById('edit-category-name').value.trim();
    const description = document.getElementById('edit-category-description').value.trim();
    
    let xhr = new XMLHttpRequest();
    xhr.open("PUT", "/resto/categories/" + id, true);
    xhr.setRequestHeader("Content-Type", "application/json");
    xhr.setRequestHeader("X-CSRF-TOKEN", document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
    xhr.setRequestHeader("Accept", "application/json");

    xhr.onreadystatechange = function() {
        if (this.readyState === 4) {
            if (this.status === 200) {
                let data = JSON.parse(this.responseText);
                if (data.success) {
                    closeEditCategoryModal();
                    location.reload(); 
                } else {
                    let errorEl = document.getElementById('edit-category-error');
                    errorEl.textContent = data.message || 'Gagal update.';
                    errorEl.classList.remove('hidden');
                }
            }
        }
    };
    xhr.send(JSON.stringify({ name: name, description: description }));
}

// 5. FITUR VARIAN
function addVariantGroup() {
    const container = document.getElementById('variant-container');
    const empty = document.getElementById('variant-empty');
    if (!container) return;
    if (empty) empty.remove();

    const index = container.querySelectorAll('.variant-group').length;
    const template = document.getElementById('variant-group-template').innerHTML;
    const html = template.replaceAll('INDEX', index);

    const div = document.createElement('div');
    div.innerHTML = html;
    const groupEl = div.firstElementChild;
    groupEl.dataset.index = index;
    container.appendChild(groupEl);
    addVariantOption(groupEl.querySelector('button[onclick="addVariantOption(this)"]'));
}

function removeVariantGroup(btn) {
    const groupEl = btn.closest('.variant-group');
    const container = document.getElementById('variant-container');
    groupEl.remove();

    const groups = container.querySelectorAll('.variant-group');
    groups.forEach((g, i) => {
        g.dataset.index = i;
        g.querySelectorAll('input[name^="variants"]').forEach(input => {
            input.name = input.name.replace(/variants\[[^\]]+\]/, `variants[${i}]`);
        });
        reindexOptions(g);
    });

    if (groups.length === 0) {
        container.innerHTML = '<p id="variant-empty" class="text-xs text-gray-400 text-center py-6 border-2 border-dashed border-gray-200 rounded-xl">Belum ada varian.</p>';
    }
}

function addVariantOption(btn) {
    const groupEl = btn.closest('.variant-group');
    const groupIndex = groupEl.dataset.index;
    const optionsContainer = groupEl.querySelector('.variant-options');
    const optionIndex = optionsContainer.querySelectorAll('.variant-option').length;

    const template = document.getElementById('variant-option-template').innerHTML;
    let html = template.replaceAll('INDEX', groupIndex).replaceAll('OPTION_INDEX', optionIndex);

    const div = document.createElement('div');
    div.innerHTML = html;
    optionsContainer.appendChild(div.firstElementChild);
    reindexOptions(groupEl);
}

function removeVariantOption(btn) {
    const groupEl = btn.closest('.variant-group');
    btn.closest('.variant-option').remove();
    reindexOptions(groupEl);
}

function reindexOptions(groupEl) {
    const groupIndex = groupEl.dataset.index;
    const options = groupEl.querySelectorAll('.variant-option');
    options.forEach((opt, i) => {
        opt.querySelectorAll('input').forEach(input => {
            input.name = input.name.replace(/variants\[[^\]]+\]\[options\]\[[^\]]+\]/, `variants[${groupIndex}][options][${i}]`);
        });
    });
}

// 6. FITUR CROP FOTO MENU
let cropper = null;

function openCropModal(event) {
    const file = event.target.files[0];
    if (!file) return;
    if (!file.type.startsWith('image/')) { alert('File harus gambar.'); return; }

    const reader = new FileReader();
    reader.onload = function(e) {
        const img = document.getElementById('crop-image');
        img.src = e.target.result;
        const modal = document.getElementById('crop-modal');
        if (modal) { modal.classList.remove('hidden'); modal.classList.add('flex'); }

        img.onload = function() {
            if (cropper) cropper.destroy();
            cropper = new Cropper(img, { aspectRatio: 1, viewMode: 1, autoCropArea: 1 });
        };
    };
    reader.readAsDataURL(file);
}

function closeCropModal() {
    const modal = document.getElementById('crop-modal');
    if (modal) { modal.classList.add('hidden'); modal.classList.remove('flex'); }
    if (cropper) { cropper.destroy(); cropper = null; }
    document.getElementById('image-input').value = '';
}

function applyCrop() {
    if (!cropper) return;
    const canvas = cropper.getCroppedCanvas({ width: 800, height: 800 });
    const dataUrl = canvas.toDataURL('image/jpeg', 0.9);
    document.getElementById('cropped-image-data').value = dataUrl;
    document.getElementById('preview-container').innerHTML = '<img src="' + dataUrl + '" class="w-full h-full object-cover rounded-lg">';
    document.getElementById('image-input').value = '';
    closeCropModal();
}

// 7. FITUR HALAMAN PENGATURAN (SETTINGS)
let logoCropper = null;

window.openLogoModal = function() {
    const modal = document.getElementById('logo-modal');
    if (modal) { modal.classList.remove('hidden'); modal.classList.add('flex'); }
}

window.closeLogoModal = function() {
    const modal = document.getElementById('logo-modal');
    if (modal) { modal.classList.add('hidden'); modal.classList.remove('flex'); }
    if (logoCropper) { logoCropper.destroy(); logoCropper = null; }
}

window.loadLogoToCrop = function(event) {
    const file = event.target.files[0];
    if (!file) return;

    const reader = new FileReader();
    reader.onload = function(e) {
        const img = document.getElementById('crop-logo-image');
        if (!img) return;
        img.src = e.target.result;
        img.onload = function() {
            if (logoCropper) logoCropper.destroy();
            logoCropper = new Cropper(img, { aspectRatio: 1, viewMode: 1, autoCropArea: 1 });
        };
    };
    reader.readAsDataURL(file);
}

window.applyLogo = function() {
    if (!logoCropper) {
        alert('Pilih foto dulu.');
        return;
    }
    
    const canvas = logoCropper.getCroppedCanvas({ width: 400, height: 400 });
    const dataUrl = canvas.toDataURL('image/jpeg', 0.9);

    const container = document.querySelector('#logo-preview, #logo-placeholder').parentElement;
    document.getElementById('logo-preview')?.remove();
    document.getElementById('logo-placeholder')?.remove();
    
    const img = document.createElement('img');
    img.id = 'logo-preview';
    img.src = dataUrl;
    img.className = 'w-full h-full object-cover';
    container.appendChild(img);

    document.getElementById('logo-file-data').value = dataUrl;
    window.closeLogoModal();
}

window.previewQris = function(event) {
    const file = event.target.files[0];
    if (!file) return;

    const reader = new FileReader();
    reader.onload = function(e) {
        const preview = document.getElementById('qris-preview');
        if (preview) {
            preview.src = e.target.result;
            document.getElementById('qris-preview-container').classList.remove('hidden');
            document.getElementById('qris-file-data').value = e.target.result;
        }
    };
    reader.readAsDataURL(file);
}

window.openPinModal = function() {
    const modal = document.getElementById('pin-modal');
    if (modal) { modal.classList.remove('hidden'); modal.classList.add('flex'); }
}

window.closePinModal = function() {
    const modal = document.getElementById('pin-modal');
    if (modal) { modal.classList.add('hidden'); modal.classList.remove('flex'); }
}

// 8. FITUR RESET PIN (PENGATURAN)
window.openPinResetModal = function() {
    document.getElementById('pin-reset-modal').classList.remove('hidden');
    document.getElementById('pin-reset-modal').classList.add('flex');
    document.getElementById('pin-reset-step-1').classList.remove('hidden');
    document.getElementById('pin-reset-step-2').classList.add('hidden');
}

window.closePinResetModal = function() {
    document.getElementById('pin-reset-modal').classList.add('hidden');
    document.getElementById('pin-reset-modal').classList.remove('flex');
}

window.sendPinOtp = function() {
    const btn = document.getElementById('btn-send-otp');
    btn.textContent = 'Mengirim...';
    btn.disabled = true;

    const url = document.getElementById('btn-send-otp').getAttribute('data-url') || '/resto/settings/send-pin-otp';

    fetch(url, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json'
        }
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            showToast('OTP telah dikirim ke email Anda.', 'success');
            document.getElementById('pin-reset-step-1').classList.add('hidden');
            document.getElementById('pin-reset-step-2').classList.remove('hidden');
        } else {
            alert(data.message);
            btn.textContent = 'Kirim OTP ke Email';
            btn.disabled = false;
        }
    })
    .catch(() => {
        showToast('Terjadi kesalahan.', 'error');
        btn.textContent = 'Kirim OTP ke Email';
        btn.disabled = false;
    });
}

window.submitResetPin = function() {
    const otp = document.getElementById('reset-otp').value;
    const newPin = document.getElementById('reset-pin').value;

    if (otp.length !== 6 || newPin.length !== 6) {
        alert('OTP dan PIN harus 6 digit.');
        return;
    }

    const url = document.getElementById('btn-send-otp').getAttribute('data-reset-url') || '/resto/settings/reset-pin';

    fetch(url, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json'
        },
        body: JSON.stringify({ otp: otp, new_pin: newPin })
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            alert('PIN berhasil direset!');
            window.closePinResetModal();
        } else {
            alert(data.message);
        }
    });
}