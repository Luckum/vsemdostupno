<label for="product-category-id" class="control-label">Категория</label>
<select id="product-category-id" class="form-control" name="Product[category_id]">
    <option value="0" selected disabled>Выберите категорию товара</option>
    <?php foreach ($categories as $cat):?>
        <option value="<?= $cat->category->id; ?>"><?= $cat->category->name; ?></option>
    <?php endforeach; ?>
</select>