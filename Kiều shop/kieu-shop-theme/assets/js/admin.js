/**
 * admin.js — KIỀU Admin Panel
 * Xử lý: save options (AJAX), upload media, lookbook CRUD,
 *         VIP notes, sidebar toggle, media picker
 */
(function ($) {
    'use strict';

    const AJAX = kieuAdminData.ajaxUrl;
    const NONCE = kieuAdminData.nonce;

    // ── NOTICE HELPER ────────────────────────────────────────────────────
    function showNotice(msg, type = 'success') {
        const $n = $('#kadNotice');
        $n.removeClass('success error').addClass(type).text(msg).show();
        setTimeout(() => $n.fadeOut(400), 3500);
    }

    // ── SIDEBAR TOGGLE (Mobile) ───────────────────────────────────────────
    $('#kadSidebarToggle').on('click', function () {
        $('.kieu-admin-sidebar').toggleClass('is-open');
    });

    $(document).on('click', function (e) {
        const $sidebar = $('.kieu-admin-sidebar');
        if ($sidebar.hasClass('is-open') &&
            !$sidebar.is(e.target) &&
            $sidebar.has(e.target).length === 0 &&
            !$('#kadSidebarToggle').is(e.target)) {
            $sidebar.removeClass('is-open');
        }
    });

    // ── SAVE SINGLE OPTION ────────────────────────────────────────────────
    function saveOption(key, value, $el) {
        $el && $el.addClass('saving');
        $.post(AJAX, {
            action: 'kieu_save_option',
            nonce: NONCE,
            option_key: key,
            option_value: value,
        })
            .done(res => {
                if (res.success) {
                    showNotice('✅ Đã lưu: ' + key);
                } else {
                    showNotice('❌ ' + (res.data?.message || 'Lỗi'), 'error');
                }
            })
            .fail(() => showNotice('❌ Không kết nối được server.', 'error'))
            .always(() => $el && $el.removeClass('saving'));
    }

    // ── SAVE ALL (batch) ──────────────────────────────────────────────────
    $(document).on('click', '.kad-save-all', function () {
        const $btn = $(this);
        $btn.text('Đang lưu...').prop('disabled', true);
        const promises = [];

        $('.kad-save-option').each(function () {
            const key = $(this).data('key');
            const val = $(this).val();
            promises.push(
                $.post(AJAX, {
                    action: 'kieu_save_option',
                    nonce: NONCE,
                    option_key: key,
                    option_value: val,
                })
            );
        });

        $.when(...promises).then(() => {
            showNotice('✅ Đã lưu tất cả thay đổi!');
            $btn.text('💾 Lưu tất cả thay đổi').prop('disabled', false);
        });
    });

    // ── WORDPRESS MEDIA PICKER HELPER ────────────────────────────────────────
    // Dùng chung cho tất cả media picker — thử wp.media, fallback file input
    function openMediaPicker(onSelect) {
        // Cách 1: wp.media nếu có và không crash
        if (typeof wp !== 'undefined' && wp.media) {
            try {
                const frame = wp.media({
                    title: 'Chọn ảnh',
                    button: { text: 'Sử dụng ảnh này' },
                    multiple: false,
                    library: { type: 'image' },
                });
                frame.on('select', function () {
                    const att = frame.state().get('selection').first().toJSON();
                    onSelect(att.url, att.id);
                });
                frame.open();
                return;
            } catch (e) {
                console.warn('wp.media lỗi, dùng file upload:', e.message);
            }
        }

        // Cách 2: Fallback — static hidden file input
        const $si = $('#kadGeneralImageInput');
        $si.off('change.pickerEvent').on('change.pickerEvent', function () {
            const file = this.files[0];
            if (!file) return;
            const fd = new FormData();
            fd.append('action', 'kieu_upload_media');
            fd.append('nonce', NONCE);
            fd.append('file', file);
            $.ajax({ url: AJAX, type: 'POST', data: fd, processData: false, contentType: false })
                .done(res => {
                    if (res.success) {
                        onSelect(res.data.url, res.data.attachment_id);
                        showNotice('✅ Đã upload ảnh!');
                    } else {
                        showNotice('❌ ' + (res.data?.message || 'Lỗi upload'), 'error');
                    }
                })
                .fail(() => showNotice('❌ Lỗi kết nối.', 'error'))
                .always(() => $si.val(''));
        });
        $si[0].click();
    }

    // ── GENERIC MEDIA PICKER (Banner, Homepage images) ────────────────────
    $(document).on('click', '.kad-pick-media', function (e) {
        e.preventDefault();
        const $field = $(this).closest('.kad-media-field');
        const optKey = $field.data('option-key');
        const $preview = $field.find('.kad-media-preview');

        openMediaPicker((url, id) => {
            $preview.html('<img src="' + url + '" alt="">');
            saveOption(optKey, url, $field);
        });
    });

    // ── LOOKBOOK THUMBNAIL PICKER ⟶ dùng helper chung ─────────────────────
    $(document).on('click', '#lookbookPickMedia', function (e) {
        e.preventDefault();
        openMediaPicker((url, id) => {
            $('#lookbookThumbnailField .kad-media-preview').html('<img src="' + url + '" alt="">');
            $('#lookbookThumbnailId').val(id);
        });
    });


    // ── SAVE LOOKBOOK ──────────────────────────────────────────────────────
    $('#saveLookbookBtn').on('click', function () {
        const $btn = $(this);
        const title = $('#lookbookTitle').val().trim();
        if (!title) { showNotice('Tiêu đề không được trống!', 'error'); return; }

        $btn.text('Đang lưu...').prop('disabled', true);

        $.post(AJAX, {
            action: 'kieu_save_lookbook',
            nonce: NONCE,
            post_id: $('#lookbookPostId').val(),
            title: title,
            content: $('#lookbookContent').val(),
            thumbnail_id: $('#lookbookThumbnailId').val(),
        })
            .done(res => {
                if (res.success) {
                    showNotice('✅ Đã lưu bài Lookbook!');
                    setTimeout(() => { window.location.href = '?tab=lookbook'; }, 1000);
                } else {
                    showNotice('❌ ' + (res.data?.message || 'Lỗi'), 'error');
                    $btn.text('💾 Lưu bài').prop('disabled', false);
                }
            })
            .fail(() => {
                showNotice('❌ Lỗi kết nối.', 'error');
                $btn.text('💾 Lưu bài').prop('disabled', false);
            });
    });

    // ── DELETE LOOKBOOK ────────────────────────────────────────────────────
    $(document).on('click', '.kad-delete-lookbook', function () {
        if (!confirm('Xóa bài lookbook này? Thao tác không thể hoàn tác.')) return;
        const $btn = $(this);
        const postId = $btn.data('id');

        $.post(AJAX, {
            action: 'kieu_delete_lookbook',
            nonce: NONCE,
            post_id: postId,
        })
            .done(res => {
                if (res.success) {
                    $btn.closest('tr').fadeOut(300, function () { $(this).remove(); });
                    showNotice('✅ Đã xóa bài lookbook.');
                } else {
                    showNotice('❌ ' + (res.data?.message || 'Lỗi'), 'error');
                }
            });
    });

    // ── VIP NOTES ─────────────────────────────────────────────────────────
    $(document).on('click', '.kad-save-note', function () {
        const $btn = $(this);
        const userId = $btn.data('user-id');
        const $ta = $btn.siblings('.kad-note-input');
        const note = $ta.val().trim();

        if (!note) { showNotice('Ghi chú không được trống!', 'error'); return; }

        $btn.text('Đang lưu...').prop('disabled', true);

        $.post(AJAX, {
            action: 'kieu_save_vip_note',
            nonce: NONCE,
            user_id: userId,
            note: note,
        })
            .done(res => {
                if (res.success) {
                    showNotice('✅ Đã lưu ghi chú.');
                    $ta.val('');
                    // Append note to UI without reload
                    const now = new Date().toLocaleDateString('vi-VN');
                    const $notes = $btn.closest('.kad-vip-notes');
                    const $addNote = $btn.closest('.kad-add-note');
                    $('<div class="kad-note-item"><span class="kad-note-by">Bạn</span><span class="kad-note-time">' + now + '</span><p>' + $('<div>').text(note).html() + '</p></div>')
                        .insertBefore($addNote);
                } else {
                    showNotice('❌ ' + (res.data?.message || 'Lỗi'), 'error');
                }
            })
            .always(() => $btn.text('Lưu ghi chú').prop('disabled', false));
    });

    // ── FILE UPLOAD ────────────────────────────────────────────────────────
    const $zone = $('#kadUploadZone');
    const $input = $('#kadFileInput');
    const $grid = $('#kadMediaGrid');

    function uploadFile(file) {
        const fd = new FormData();
        fd.append('action', 'kieu_upload_media');
        fd.append('nonce', NONCE);
        fd.append('file', file);

        $.ajax({
            url: AJAX,
            type: 'POST',
            data: fd,
            processData: false,
            contentType: false,
        })
            .done(res => {
                if (res.success) {
                    showNotice('✅ Upload thành công: ' + file.name);
                    const $item = $('<div class="kad-media-item"><img alt=""><div class="kad-media-item-url"></div></div>');
                    $item.find('img').attr('src', res.data.thumb || res.data.url);
                    $item.find('.kad-media-item-url').text(file.name);
                    $grid.prepend($item);
                } else {
                    showNotice('❌ ' + (res.data?.message || 'Upload thất bại'), 'error');
                }
            });
    }

    if ($zone.length) {
        $zone.on('dragover', e => { e.preventDefault(); $zone.addClass('drag-over'); });
        $zone.on('dragleave drop', () => $zone.removeClass('drag-over'));
        $zone.on('drop', e => {
            e.preventDefault();
            const files = e.originalEvent.dataTransfer.files;
            Array.from(files).forEach(uploadFile);
        });
        $input.on('change', function () {
            Array.from(this.files).forEach(uploadFile);
        });
    }


    // ── THÊM SẢN PHẨM MỚI ──────────────────────────────────────────────────

    // Toggle form
    $('#kadToggleAddProduct').on('click', function () {
        const $form = $('#kadAddProductForm');
        if ($form.is(':visible')) {
            $form.slideUp(200);
            $(this).html('<i class="fa-solid fa-plus"></i> Thêm sản phẩm');
        } else {
            $form.slideDown(250);
            $(this).html('<i class="fa-solid fa-chevron-up"></i> Đóng form');
            $('html, body').animate({ scrollTop: $form.offset().top - 80 }, 300);
        }
    });

    // Hủy
    $('#kadCancelAddProduct').on('click', function () {
        $('#kadAddProductForm').slideUp(200);
        $('#kadToggleAddProduct').html('<i class="fa-solid fa-plus"></i> Thêm sản phẩm');
    });

    // Chọn ảnh cho sản phẩm mới
    $('#newPPickThumb').on('click', function (e) {
        e.preventDefault();
        openMediaPicker((url, id) => {
            $('#newPThumbImg').attr('src', url).show();
            $('#newPThumbPlaceholder').addClass('hidden');
            $('#newPThumbId').val(id);
        });
    });

    // Tạo sản phẩm mới
    $('#kadSaveNewProduct').on('click', function () {
        const $btn = $(this);
        const name = $('#newPName').val().trim();
        const price = $('#newPPrice').val().trim();
        const sale = $('#newPSale').val().trim();
        const stock = $('#newPStock').val().trim();
        const desc = $('#newPDesc').val().trim();
        const status = $('#newPStatus').val();
        const thumbId = $('#newPThumbId').val();

        if (!name) { showNotice('Tên sản phẩm không được trống!', 'error'); return; }

        $btn.html('<i class="fa-solid fa-spinner fa-spin"></i> Đang tạo...').prop('disabled', true);

        $.post(AJAX, {
            action: 'kieu_save_product',
            nonce: NONCE,
            product_id: 0,         // 0 = tạo mới
            name: name,
            price: price,
            sale_price: sale,
            stock: stock,
            description: desc,
            status: status,
            thumbnail_id: thumbId,
        })
            .done(res => {
                if (res.success) {
                    showNotice('✅ Đã tạo sản phẩm: ' + name);

                    // Thêm row mới vào đầu bảng
                    const statusLabel = status === 'publish' ? 'Hiện' : 'Ẩn';
                    const statusClass = status === 'publish' ? 'active' : 'draft';
                    const priceDisplay = sale
                        ? '<span style="text-decoration:line-through;color:#aaa;font-size:.78rem">' + (price ? Number(price).toLocaleString('vi-VN') : '0') + '₫</span> <strong style="color:#8C2020">' + Number(sale).toLocaleString('vi-VN') + '₫</strong>'
                        : (price ? Number(price).toLocaleString('vi-VN') + '₫' : '—');
                    const thumbHtml = thumbId && res.data.thumb_url
                        ? '<img src="' + res.data.thumb_url + '" class="kad-list-thumb" alt="">'
                        : '<div style="width:40px;height:40px;background:#F0EDED;border-radius:4px;display:flex;align-items:center;justify-content:center"><i class="fa-solid fa-image" style="color:#CCC;font-size:1rem"></i></div>';

                    const newRow = '<tr class="kad-product-row" data-id="' + (res.data.product_id || '') + '"><td>' + thumbHtml + '</td><td><strong>' + name + '</strong></td><td>' + priceDisplay + '</td><td>' + (stock || '—') + '</td><td><span class="kad-status-badge ' + statusClass + '">' + statusLabel + '</span></td><td class="kad-actions-col"><span class="kad-btn-sm" style="color:#888;">Tải lại để sửa</span></td></tr>';

                    $('#kadProductTable tbody').prepend(newRow);

                    // Reset form
                    $('#newPName, #newPPrice, #newPSale, #newPStock, #newPDesc').val('');
                    $('#newPStatus').val('publish');
                    $('#newPThumbId').val('');
                    $('#newPThumbImg').hide().attr('src', '');
                    $('#newPThumbPlaceholder').removeClass('hidden');

                    // Đóng form sau 1s
                    setTimeout(() => {
                        $('#kadAddProductForm').slideUp(200);
                        $('#kadToggleAddProduct').html('<i class="fa-solid fa-plus"></i> Thêm sản phẩm');
                    }, 1200);
                } else {
                    showNotice('❌ ' + (res.data?.message || 'Tạo sản phẩm thất bại'), 'error');
                }
            })
            .fail(() => showNotice('❌ Lỗi kết nối server.', 'error'))
            .always(() => $btn.html('<i class="fa-solid fa-floppy-disk"></i> Tạo sản phẩm').prop('disabled', false));
    });

    // ── PRODUCT INLINE EDITOR ──────────────────────────────────────────────

    // Toggle inline edit row
    $(document).on('click', '.kad-edit-product', function () {
        const pid = $(this).data('id');
        const $editRow = $('#editRow-' + pid);

        // Đóng các row khác đang mở
        $('.kad-product-edit-row').not($editRow).slideUp(200);

        // Toggle row này
        if ($editRow.is(':visible')) {
            $editRow.slideUp(200);
        } else {
            $editRow.slideDown(250);
            // Scroll đến row
            $('html, body').animate({ scrollTop: $editRow.offset().top - 80 }, 300);
        }
    });

    // Đóng inline editor
    $(document).on('click', '.kad-cancel-edit', function () {
        const pid = $(this).data('id');
        $('#editRow-' + pid).slideUp(200);
    });

    // Chọn ảnh cho sản phẩm — dùng wp.media nếu có, fallback AJAX upload
    $(document).on('click', '.kad-pick-product-thumb', function (e) {
        e.preventDefault();
        const $btn = $(this);
        const $row = $btn.closest('.kad-product-edit-row');
        const pid = $row.attr('id').replace('editRow-', '');

        function applyImage(url, id) {
            const $img = $row.find('[id^="pThumb-"]');
            $img.attr('src', url).show();
            $row.find('[id^="pThumbPlaceholder-"]').addClass('hidden');
            $row.find('.kp-thumb-id').val(id);
            // Cập nhật thumbnail trong product row
            const $productImg = $('.kad-product-row[data-id="' + pid + '"] .kad-list-thumb');
            if ($productImg.length) {
                $productImg.attr('src', url).show();
            } else {
                $('.kad-product-row[data-id="' + pid + '"] td:first').html(
                    '<img src="' + url + '" class="kad-list-thumb" alt="">'
                );
            }
        }

        // ── Cách 1: WordPress Media Library (bọc try-catch vì có thể crash)
        if (typeof wp !== 'undefined' && wp.media) {
            try {
                const frame = wp.media({
                    title: 'Chọn ảnh sản phẩm',
                    button: { text: 'Chọn' },
                    multiple: false,
                    library: { type: 'image' },
                });
                frame.on('select', function () {
                    const att = frame.state().get('selection').first().toJSON();
                    applyImage(att.url, att.id);
                });
                frame.open();
                return; // Thành công → không cần fallback
            } catch (e) {
                // wp.media crash (thường gặp trên frontend) → dùng file input fallback
                console.warn('wp.media không hoạt động, dùng file input fallback:', e.message);
            }
        }

        // ── Cách 2: Fallback — static file input + AJAX upload
        let _currentApply = applyImage;
        const $staticInput = $('#kadProductImageInput');

        // Xóa listener cũ, tránh trigger nhiều lần
        $staticInput.off('change.productPicker').on('change.productPicker', function () {
            const file = this.files[0];
            if (!file) return;

            const fd = new FormData();
            fd.append('action', 'kieu_upload_media');
            fd.append('nonce', NONCE);
            fd.append('file', file);

            $btn.html('<i class="fa-solid fa-spinner fa-spin"></i> Đang upload...').prop('disabled', true);

            $.ajax({
                url: AJAX,
                type: 'POST',
                data: fd,
                processData: false,
                contentType: false,
            })
                .done(res => {
                    if (res.success) {
                        _currentApply(res.data.url, res.data.attachment_id);
                        showNotice('✅ Đã upload ảnh thành công!');
                    } else {
                        showNotice('❌ ' + (res.data?.message || 'Upload thất bại'), 'error');
                    }
                })
                .fail(() => showNotice('❌ Lỗi kết nối khi upload.', 'error'))
                .always(() => {
                    $btn.html('<i class="fa-solid fa-image"></i> Thay ảnh').prop('disabled', false);
                    // Reset để có thể chọn lại file cùng tên
                    $staticInput.val('');
                });
        });

        // Click vào input đã có trong DOM (trình duyệt cho phép vì trong user event handler)
        $staticInput[0].click();
    });


    // Lưu sản phẩm qua AJAX
    $(document).on('click', '.kad-save-product', function () {
        const $btn = $(this);
        const pid = $btn.data('id');
        const $row = $('#editRow-' + pid);

        const name = $row.find('.kp-name').val().trim();
        const price = $row.find('.kp-price').val().trim();
        const sale = $row.find('.kp-sale').val().trim();
        const stock = $row.find('.kp-stock').val().trim();
        const desc = $row.find('.kp-desc').val().trim();
        const status = $row.find('.kp-status').val();
        const thumbId = $row.find('.kp-thumb-id').val();

        if (!name) { showNotice('Tên sản phẩm không được trống!', 'error'); return; }

        $btn.text('Đang lưu...').prop('disabled', true);

        $.post(AJAX, {
            action: 'kieu_save_product',
            nonce: NONCE,
            product_id: pid,
            name: name,
            price: price,
            sale_price: sale,
            stock: stock,
            description: desc,
            status: status,
            thumbnail_id: thumbId,
        })
            .done(res => {
                if (res.success) {
                    showNotice('✅ ' + res.data.message);
                    // Cập nhật realtime trong bảng
                    const $pRow = $('.kad-product-row[data-id="' + pid + '"]');
                    $pRow.find('strong').first().text(name);
                    const priceCell = $pRow.find('td').eq(2);
                    if (sale) {
                        priceCell.html('<span style="text-decoration:line-through;color:#aaa;font-size:0.78rem">' + res.data.price + '₫</span> <strong style="color:#8C2020">' + res.data.sale + '₫</strong>');
                    } else {
                        priceCell.text(res.data.price + '₫');
                    }
                    // Status badge
                    const $badge = $pRow.find('.kad-status-badge');
                    if (status === 'publish') {
                        $badge.removeClass('draft').addClass('active').text('Hiện');
                    } else {
                        $badge.removeClass('active').addClass('draft').text('Ẩn');
                    }
                    // Đóng editor sau 800ms
                    setTimeout(() => { $row.slideUp(200); }, 800);
                } else {
                    showNotice('❌ ' + (res.data?.message || 'Lỗi.'), 'error');
                }
            })
            .fail(() => showNotice('❌ Lỗi kết nối server.', 'error'))
            .always(() => $btn.html('<i class="fa-solid fa-floppy-disk"></i> Lưu sản phẩm').prop('disabled', false));
    });

})(jQuery);
