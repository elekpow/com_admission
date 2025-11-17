<?php
defined('_JEXEC') or die;

use Joomla\CMS\Router\Route;
use Joomla\CMS\Session\Session;

$isNew = $this->item->id == 0;
$title = $isNew ? 'Добавить заявку' : 'Редактировать заявку';
?>
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <h1><?php echo $title; ?> #<?php echo $this->item->id ?: 'новая'; ?></h1>
            
            <div class="mb-3">
                <a href="<?php echo Route::_('index.php?option=com_admission&view=items'); ?>" 
                   class="btn btn-outline-secondary">
                    <i class="icon-arrow-left"></i> Назад к списку
                </a>
            </div>

            <form action="<?php echo Route::_('index.php?option=com_admission'); ?>" 
                  method="post" name="adminForm" id="item-form" class="form-horizontal">

                <div class="card">
                    <div class="card-header">
                        <h3>Основная информация</h3>
                    </div>
                    <div class="card-body">
                        <div class="form-group">
                            <label for="title" class="control-label">
                                Название заявки *
                            </label>
                            <input type="text" name="title" id="title" 
                                   class="form-control" 
                                   required 
                                   value="<?php echo htmlspecialchars($this->item->title); ?>"
                                   placeholder="Введите название заявки" />
                        </div>

                        <div class="form-group">
                            <label for="email" class="control-label">
                                Email адрес *
                            </label>
                            <input type="email" name="email" id="email" 
                                   class="form-control" 
                                   required 
                                   value="<?php echo htmlspecialchars($this->item->email); ?>"
                                   placeholder="Введите email адрес" />
                        </div>

                        <div class="form-group">
                            <label for="phone" class="control-label">
                                Телефон
                            </label>
                            <input type="text" name="phone" id="phone" 
                                   class="form-control" 
                                   value="<?php echo htmlspecialchars($this->item->phone); ?>"
                                   placeholder="Введите номер телефона" />
                        </div>

                        <div class="form-group">
                            <label for="status" class="control-label">
                                Статус заявки
                            </label>
                            <select name="status" id="status" class="form-control">
                                <option value="pending" <?php echo $this->item->status == 'pending' ? 'selected' : ''; ?>>Ожидает рассмотрения</option>
                                <option value="in_review" <?php echo $this->item->status == 'in_review' ? 'selected' : ''; ?>>На рассмотрении</option>
                                <option value="approved" <?php echo $this->item->status == 'approved' ? 'selected' : ''; ?>>Одобрена</option>
                                <option value="rejected" <?php echo $this->item->status == 'rejected' ? 'selected' : ''; ?>>Отклонена</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="description" class="control-label">
                                Описание
                            </label>
                            <textarea name="description" id="description" 
                                      class="form-control" 
                                      rows="5" 
                                      placeholder="Дополнительная информация о заявке"><?php echo htmlspecialchars($this->item->description); ?></textarea>
                        </div>
                    </div>
                </div>

                <div class="card mt-3">
                    <div class="card-header">
                        <h3>Публикация</h3>
                    </div>
                    <div class="card-body">
                        <div class="form-group">
                            <label for="state" class="control-label">
                                Статус публикации
                            </label>
                            <select name="state" id="state" class="form-control">
                                <option value="1" <?php echo $this->item->state == 1 ? 'selected' : ''; ?>>Опубликовано</option>
                                <option value="0" <?php echo $this->item->state == 0 ? 'selected' : ''; ?>>Не опубликовано</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="mt-3">
                    <button type="submit" name="task" value="item.apply" class="btn btn-info">
                        <i class="icon-apply"></i> Применить
                    </button>
                    <button type="submit" name="task" value="item.save" class="btn btn-success">
                        <i class="icon-save"></i> Сохранить
                    </button>
                    <?php if ($isNew): ?>
                    <button type="submit" name="task" value="item.save2new" class="btn btn-primary">
                        <i class="icon-save"></i> Сохранить и создать новую
                    </button>
                    <?php endif; ?>
                    <a href="<?php echo Route::_('index.php?option=com_admission&view=items'); ?>" class="btn btn-danger">
                        <i class="icon-cancel"></i> Отмена
                    </a>
                </div>

                <input type="hidden" name="id" value="<?php echo $this->item->id; ?>" />
                <input type="hidden" name="option" value="com_admission" />
                <input type="hidden" name="<?php echo Session::getFormToken(); ?>" value="1" />
            </form>
        </div>
    </div>
</div>

<script>
document.getElementById('item-form').addEventListener('submit', function(e) {
    var title = document.getElementById('title').value.trim();
    var email = document.getElementById('email').value.trim();
    
    if (!title) {
        alert('Пожалуйста, введите название заявки');
        e.preventDefault();
        return false;
    }
    
    if (!email) {
        alert('Пожалуйста, введите email адрес');
        e.preventDefault();
        return false;
    }
    
    if (!isValidEmail(email)) {
        alert('Пожалуйста, введите корректный email адрес');
        e.preventDefault();
        return false;
    }
});

function isValidEmail(email) {
    var re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return re.test(email);
}
</script>