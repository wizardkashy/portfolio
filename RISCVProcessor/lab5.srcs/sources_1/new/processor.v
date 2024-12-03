`timescale 1ns / 1ps
//////////////////////////////////////////////////////////////////////////////////
// Company: 
// Engineer: 
// 
// Create Date: 06/02/2024 10:35:14 PM
// Design Name: 
// Module Name: processor
// Project Name: 
// Target Devices: 
// Tool Versions: 
// Description: 
// 
// Dependencies: 
// 
// Revision:
// Revision 0.01 - File Created
// Additional Comments:
// 
//////////////////////////////////////////////////////////////////////////////////


module processor(
    input clk, reset,
    output [31:0] Result
    );
    wire [2:0] Funct3;
    wire [6:0] Funct7;
    wire [6:0] Opcode;
    wire [2:0] ALUOp;
    wire [3:0] Operation;
    
    wire RegWrite, ALUSrc, MemRead, MemWrite, MemtoReg;
    Controller controller(Opcode, ALUOp, RegWrite, ALUSrc, MemRead, MemWrite, MemtoReg);
    ALUController alucontroller(ALUOp, Funct7, Funct3, Operation);
    data_path datapath(clk, reset, RegWrite, MemtoReg, ALUSrc, MemWrite, MemRead, Operation, Opcode, Funct7, Funct3, Result);
    
endmodule
