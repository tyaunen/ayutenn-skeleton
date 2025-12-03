/**
 * FileUploader
 * DropZone.jsみたいなの
 */
class FileUploader {
    constructor(options) {
        // デフォルト設定とマージ
        this.options = Object.assign({
            containerId: null, // コンテナのID（必須）
            endpoint: '/oreore/upload_icon', // アップロード先エンドポイント
            maxFileSize: 5 * 1024 * 1024, // デフォルト最大ファイルサイズ (5MB)
            acceptedTypes: null, // 許容するファイルタイプ (例: 'image/*')
            multiple: true, // 複数ファイルの許可
            onUploadComplete: null, // アップロード完了時のコールバック
            onError: null // エラー発生時のコールバック
        }, options);

        // 必須パラメータの検証
        if (!this.options.containerId) {
            console.error('コンテナIDが指定されていません');
            return;
        }

        this.container = document.getElementById(this.options.containerId);
        if (!this.container) {
            console.error(`ID ${this.options.containerId} のコンテナが見つかりません`);
            return;
        }

        this.initialize();
    }

    /**
     * コンポーネント初期化
     */
    initialize() {
        const input = document.createElement('input');
        input.type = 'file';
        input.style = 'display: none;';

        if (this.options.multiple) {
            input.multiple = true;
        }

        if (this.options.acceptedTypes) {
            input.accept = this.options.acceptedTypes;
        }

        this.container.appendChild(input);
        this.fileInput = input;

        // イベントリスナーの設定
        this.setupEventListeners();
    }

    /**
     * イベントリスナーの設定
     */
    setupEventListeners() {
        // ドラッグ＆ドロップイベント
        ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
            this.container.addEventListener(eventName, e => this.preventDefaults(e));
        });

        // ハイライト効果
        ['dragenter', 'dragover'].forEach(eventName => {
            this.container.addEventListener(eventName, () => this.highlight());
        });

        ['dragleave', 'drop'].forEach(eventName => {
            this.container.addEventListener(eventName, () => this.unhighlight());
        });

        // ファイルのドロップとクリックイベント
        this.container.addEventListener('drop', e => this.handleDrop(e));
        this.container.addEventListener('click', () => this.fileInput.click()); // display: none;なinputのイベントをドロップエリアにバインド
        this.fileInput.addEventListener('change', e => this.handleFileInputChange(e));
    }

    /**
     * デフォルト動作の防止
     */
    preventDefaults(e) {
        e.preventDefault();
        e.stopPropagation();
    }

    /**
     * ドラッグ時のハイライト効果を追加
     */
    highlight() {
        this.container.classList.add('highlight');
    }

    /**
     * ハイライト効果を解除
     */
    unhighlight() {
        this.container.classList.remove('highlight');
    }

    /**
     * ファイルドロップ時の処理
     */
    handleDrop(e) {
        const dt = e.dataTransfer;
        this.handleFiles(dt.files);
    }

    /**
     * ファイル入力変更時の処理
     */
    handleFileInputChange(e) {
        this.handleFiles(e.target.files);
    }

    /**
     * ファイル処理のメイン関数
     */
    handleFiles(fileList) {
        const files = Array.from(fileList);

        if (files.length === 0) {
            return;
        }

        files.forEach(file => {
            // ファイルサイズの検証
            if (file.size > this.options.maxFileSize) {
                this.showError(`${file.name} は最大サイズ (${this.formatFileSize(this.options.maxFileSize)}) を超えています`);
                return;
            }

            // ファイルタイプの検証（設定されている場合）
            if (this.options.acceptedTypes) {
                const fileType = file.type;
                const acceptedTypes = this.options.acceptedTypes.split(',');
                let isAccepted = false;

                for (const type of acceptedTypes) {
                    // ワイルドカード対応（例: image/* のようなケース）
                    if (type.endsWith('/*')) {
                        const baseType = type.substring(0, type.indexOf('/*'));
                        if (fileType.startsWith(baseType)) {
                            isAccepted = true;
                            break;
                        }
                    } else if (type === fileType) {
                        isAccepted = true;
                        break;
                    }
                }

                if (!isAccepted) {
                    this.showError(`${file.name} は許可されたファイルタイプではありません`);
                    return;
                }
            }

            // ファイルをアップロード
            this.uploadFile(file);
        });
    }

    /**
     * ファイルのアップロード処理
     */
    uploadFile(file) {
        const formData = new FormData();
        formData.append('files[]', file);

        const xhr = new XMLHttpRequest();
        xhr.open('POST', this.options.endpoint, true);

        xhr.onload = () => {
            if (xhr.status === 200) {
                try {
                    const response = JSON.parse(xhr.responseText);

                    if (response.status === 0) {
                        // 完了コールバック
                        if (typeof this.options.onUploadComplete === 'function') {
                            this.options.onUploadComplete(file, response);
                        }
                    } else {
                        // APIエラー
                        this.showError(response.message || '不明なエラーが発生しました');
                    }
                } catch (e) {
                    // JSON解析エラー
                    this.showError('レスポンスの解析に失敗しました');
                }
            } else {
                // HTTP通信エラー
                this.showError(`通信エラー: ${xhr.status} ${xhr.statusText}`);
            }
        };

        xhr.onerror = () => {
            this.showError('ネットワークエラーが発生しました');
        };

        xhr.send(formData);
    }

    /**
     * エラーメッセージ表示
     */
    showError(message) {
        // エラーコールバック
        if (typeof this.options.onError === 'function') {
            this.options.onError(message);
        }
    }

    /**
     * ファイルサイズのフォーマット
     */
    formatFileSize(bytes) {
        if (bytes === 0) return '0 Bytes';

        const k = 1024;
        const sizes = ['Bytes', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));

        return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
    }
}