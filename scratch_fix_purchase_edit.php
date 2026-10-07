<?php
$file = 'c:/xampp/htdocs/atif_traderss/resources/views/admin_panel/purchase/edit.blade.php';
$content = file_get_contents($file);

// 1. Header
$oldHeader = '<th class="col-qty">Boxes</th>
                                            <th class="col-stock">Pack Size</th>';
$newHeader = '<th class="col-qty">Cartons</th>
                                            <th class="col-qty">Loose Pcs</th>
                                            <th class="col-stock">Pack Size</th>';
$content = str_replace($oldHeader, $newHeader, $content);

// 2. Foreach Loop Row
$oldLoopRow = '{{-- Hidden Box/Loose Calc fields --}}
                                                    <input type="hidden" name="boxes_qty[]" class="hidden-boxes-qty"
                                                        value="{{ $boxes }}">
                                                    <input type="hidden" name="loose_qty[]" class="hidden-loose-qty"
                                                        value="{{ $loose }}">
                                                </td>
                                                <td>
                                                    <input type="text" class="form-control box-qty"
                                                        value="{{ $displayBoxes }}" placeholder="Boxes">
                                                </td>
                                                <td>
                                                    <input type="number" class="form-control input-readonly pack-size"
                                                        value="{{ $ppb }}" readonly>
                                                </td>';

$newLoopRow = '</td>
                                                <td>
                                                    <input type="number" class="form-control carton-qty" name="boxes_qty[]"
                                                        value="{{ $boxes }}" placeholder="Cartons" min="0">
                                                </td>
                                                <td>
                                                    <input type="number" class="form-control loose-qty" name="loose_qty[]"
                                                        value="{{ $loose }}" placeholder="Loose Pcs" min="0">
                                                </td>
                                                <td>
                                                    <input type="number" class="form-control input-readonly pack-size" name="pieces_per_box_display[]"
                                                        value="{{ $ppb }}" readonly>
                                                </td>';
$content = str_replace($oldLoopRow, $newLoopRow, $content);

// 3. addBlankRow
$oldAddRow = '<input type="hidden" name="boxes_qty[]" class="hidden-boxes-qty" value="0">
                        <input type="hidden" name="loose_qty[]" class="hidden-loose-qty" value="0">
                    </td>
                    <td><input type="text" class="form-control box-qty" placeholder="Boxes"></td>
                    <td><input type="number" class="form-control input-readonly pack-size" value="1" readonly></td>';
$newAddRow = '</td>
                    <td><input type="number" class="form-control carton-qty" name="boxes_qty[]" value="0" placeholder="Cartons" min="0"></td>
                    <td><input type="number" class="form-control loose-qty" name="loose_qty[]" value="0" placeholder="Loose Pcs" min="0"></td>
                    <td><input type="number" class="form-control input-readonly pack-size" name="pieces_per_box_display[]" value="1" readonly></td>';
$content = str_replace($oldAddRow, $newAddRow, $content);

// 4. Events
$oldEvent = "$('#purchaseTableBody').on('input', '.box-qty, .price, .item-disc-percent, .item-disc-amt', function() {
                if ($(this).hasClass('box-qty')) {
                    normalizeQtyInput($(this), $(this).closest('tr'));
                }
                recalcRow($(this).closest('tr'));
                recalcAll();
            });";
$newEvent = "$('#purchaseTableBody').on('input', '.carton-qty, .loose-qty, .price, .item-disc-percent, .item-disc-amt', function() {
                recalcRow($(this).closest('tr'));
                recalcAll();
            });";
$content = str_replace($oldEvent, $newEvent, $content);

// 5. recalcRow
$oldRecalcRow = "            function recalcRow(\$row) {
                let boxesStr = \$row.find('.box-qty').val();
                if (!boxesStr) boxesStr = \"0\";
                boxesStr = boxesStr.toString();

                const ppb = parseFloat(\$row.find('.pack-size').val()) || 1;
                const sizeMode = \$row.data('sizemode') || \$row.find('.hidden-size-mode').val();
                const pieces_per_m2 = parseFloat(\$row.data('pieces_per_m2')) || parseFloat(\$row.find(
                    '.hidden-pieces-per-m2').val()) || 0;

                let boxes = 0;
                let loose = 0;
                let totalPieces = 0;

                if (ppb > 1 && boxesStr.includes('.')) {
                    const parts = boxesStr.split('.');
                    boxes = parseInt(parts[0]) || 0;
                    loose = parts[1] ? parseInt(parts[1]) : 0;
                    totalPieces = (boxes * ppb) + loose;
                } else {
                    boxes = parseFloat(boxesStr) || 0;
                    totalPieces = boxes * ppb;
                }

                // Update hidden separate fields
                \$row.find('.hidden-boxes-qty').val(boxes);
                \$row.find('.hidden-loose-qty').val(loose);

                \$row.find('.qty-pcs').val(totalPieces);";

$newRecalcRow = "            function recalcRow(\$row) {
                const ppb = parseFloat(\$row.find('.pack-size').val()) || 1;
                const sizeMode = \$row.data('sizemode') || \$row.find('.hidden-size-mode').val();
                const pieces_per_m2 = parseFloat(\$row.data('pieces_per_m2')) || parseFloat(\$row.find('.hidden-pieces-per-m2').val()) || 0;

                // Read separate Carton + Loose inputs
                const cartons = parseInt(\$row.find('.carton-qty').val()) || 0;
                let loose = parseInt(\$row.find('.loose-qty').val()) || 0;

                // Auto-convert excess loose into cartons
                if (loose >= ppb && ppb > 1) {
                    const extraCartons = Math.floor(loose / ppb);
                    loose = loose % ppb;
                    \$row.find('.carton-qty').val(cartons + extraCartons);
                    \$row.find('.loose-qty').val(loose);
                }

                const totalPieces = (cartons * ppb) + loose;

                // Update the readonly Pieces field (sent as qty[])
                \$row.find('.qty-pcs').val(totalPieces);";
$content = str_replace($oldRecalcRow, $newRecalcRow, $content);

file_put_contents($file, $content);
echo "Modifications applied.\n";
