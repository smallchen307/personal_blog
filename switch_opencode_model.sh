#!/bin/bash

# OpenCode Ollama Model Switcher
# 用法: ./switch_model.sh [qwen30b|starcoder3b]

MODEL_NAME="$1"

case "$MODEL_NAME" in
    "qwen30b")
        MODEL="qwen3-coder:30b"
        echo "切換到 Qwen3-Coder 30B (高品質)..."
        ;;
    "starcoder3b")
        MODEL="starcoder2:3b"
        echo "切換到 StarCoder2 3B (快速)..."
        ;;
    *)
        echo "用法: $0 [qwen30b|starcoder3b]"
        echo ""
        echo "可用模型:"
        echo "  qwen30b     - Qwen3-Coder 30B (高品質，18GB)"
        echo "  starcoder3b - StarCoder2 3B (快速，1.7GB)"
        exit 1
        ;;
esac

# 創建設置文件
cat > "/Users/smallchen307/Library/Application Support/ai.opencode.desktop/opencode.settings.json" << EOF
{
  "llm": {
    "provider": "openai",
    "apiBase": "http://localhost:11434/v1",
    "apiKey": "ollama",
    "model": "$MODEL"
  }
}
EOF

echo "✅ 已切換到 $MODEL"
echo "請重新啟動 OpenCode 使設置生效"